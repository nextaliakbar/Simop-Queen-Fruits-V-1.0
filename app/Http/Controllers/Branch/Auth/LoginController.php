<?php

namespace App\Http\Controllers\Branch\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest:branch', ['except' => ['logout']]);
    }

    public function captcha($tmp)
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if(Session::has('default_captcha_code')) {
            Session::forget('default_captcha_code');
        }

        Session::put('default_captcha_code', $phrase);
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: image/jpeg');

        $builder->output();
    }

    public function login(): Renderable
    {
        $logo_name = Helpers::get_business_settings('logo');
        $logo = Helpers::on_error_image($logo_name, asset('storage/store') . '/' . $logo_name, asset('assets/admin/img/logo_store_2.png'), 'store/');
        return view('branch-views.auth.login', compact('logo'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');

        if(isset($recaptcha) && $recaptcha['status'] == 1 && !$request?->set_default_capctha) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secrect_key = Helpers::get_business_settings('recaptcha');

                        $response = $value;

                        $g_response = Http::asForm()->post('https://www.google.com/recaptcha/api/sitverify', [
                            'secrect' => $secrect_key,
                            'value' => $value,
                            'remotip' => \request()->ip()
                        ]);

                        if(!$g_response->successful()) {
                            $fail("Captcha tidak valid");
                        }
                    }
                ]
            ]);
        } else {
            if(strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code'))) {
                Session::forget('default_captcha_code');
                return back()->withErrors("Captcha tidak valid");
            }
        }

        if(Session::has('default_captcha_code')) {
            Session::forget('default_captcha_code');
        }

        if(auth('branch')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])) {
            return redirect()->route('branch.dashboard');
        }

        return redirect()->back()->withInput($request->only('email'))
        ->withErrors(["Akun tidak ditemukan"]);
    }

    public function logout(): RedirectResponse
    {
        auth()->guard('branch')->logout();
        return redirect()->route('branch.auth.login');
    }
}
