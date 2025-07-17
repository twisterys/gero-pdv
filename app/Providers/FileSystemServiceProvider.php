<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class FileSystemServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configureExternalStorage();
    }

    /**
     * Dynamically configure the external storage disk based on the operating system.
     *
     * @return void
     */
    protected function configureExternalStorage()
    {
        $os = php_uname();
        if (strpos($os, 'Windows') !== false) {
            Config::set('filesystems.disks.external_storage.root', 'C:\laragon\www\gero-storage');
        } else {
            Config::set('filesystems.disks.external_storage.root', '/home/gero_storage');

        }
    }
}
