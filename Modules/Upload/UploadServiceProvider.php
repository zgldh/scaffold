<?php namespace Modules\Upload;

use App\Scaffold\GraphQL\GraphQL;
use Illuminate\Support\ServiceProvider;
use Modules\Upload\Commands\CleanUnUsedUploads;
use Modules\Upload\GraphQL\Queries\UploadsQuery;
use Modules\Upload\GraphQL\Types\UploadType;
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
        $this->registerCommands();
        $this->registerGraphQL();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views',
            'Modules\Upload');

        Upload::deleted(
            function ($upload) {
                //
                $upload->deleteFile(false);
            }
        );
    }

    private function registerCommands()
    {
        $this->app->singleton('upload.clean', function ($app) {
            return new CleanUnUsedUploads();
        });

        $this->commands([
            'upload.clean'
        ]);
    }

    private function registerGraphQL()
    {
        GraphQL::addSchema([
            'query'    => [
                'uploads' => UploadsQuery::class
            ],
            'mutation' => [
                //'updateUserEmail' => 'App\GraphQL\Query\UpdateUserEmailMutation'
            ]
        ]);
    }
}