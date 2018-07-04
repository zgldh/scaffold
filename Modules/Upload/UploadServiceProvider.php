<?php namespace Modules\Upload;

use Illuminate\Support\ServiceProvider;
use Modules\Upload\Models\Upload;

class UploadServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Upload::deleted(
            function ($upload) {
                //
                $upload->deleteFile(false);
            }
        );

    }
}