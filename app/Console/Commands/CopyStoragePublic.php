<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyStoragePublic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-storage-public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = storage_path('app/public');
        $destination = public_path('storage');

        if(!File::exists($source)) {
            $this->error('Source directory does not exist.');
            return;
        }

        if(!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        File::copyDirectory($source, $destination);

        $this->info('Files copied successfully from storage/app/public to public/storage.');
    }
}
