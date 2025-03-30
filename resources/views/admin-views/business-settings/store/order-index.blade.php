@extends('layouts.admin.app')

@section('title', 'Pengaturan Bisnis')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title">
                    Pengaturan Bisnis
                </span>
            </h2>
        </div>

        @include('admin-views.business-settings.partials._business-setup-inline-menu')

        <form action="{{route('admin.business-settings.store.order-update')}}" method="post">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="mb-0">
                        Pengaturan Pesanan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php($mov=\App\Models\BusinessSetting::where('key','minimum_order_value')->first()->value)
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">
                                    Min. Order (Rp)
                                </label>
                                <input type="number" min="1" value="{{$mov}}"
                                       name="minimum_order_value" class="form-control" placeholder="Contoh : 1000"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            @php($scheduleOrderSlotDuration=\App\CentralLogics\Helpers::get_business_settings('schedule_order_slot_duration'))
                            <div class="form-group">
                                <label class="input-label text-capitalize" for="schedule_order_slot_duration">Jadwal Slot Durasi Pesanan (Menit)</label>
                                <input type="number" name="schedule_order_slot_duration" class="form-control" id="schedule_order_slot_duration" value="{{$scheduleOrderSlotDuration?$scheduleOrderSlotDuration:0}}" min="1" placeholder="Contoh : 30" required>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container">
                        <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary call-demo">Simpan</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

