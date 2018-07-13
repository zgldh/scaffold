<?php namespace Modules\Notification;

use Illuminate\Support\ServiceProvider;
use Modules\Notification\Commands\CreateNotification;

class NotificationServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return  void
     */
    public function register()
    {
        //
        $this->registerCommands();
    }

    /**
     * Bootstrap any application services.
     *
     * @return  void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views',
            'Modules\Notification');
    }

    private function registerCommands()
    {
        $this->app->singleton('notifications.create', function ($app) {
            return $app[CreateNotification::class];
        });

        $this->commands([
            'notifications.create'
        ]);
    }
}