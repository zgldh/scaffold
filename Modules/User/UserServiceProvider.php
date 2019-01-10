<?php namespace Modules\User;

use App\Scaffold\GraphQL\GraphMaker;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\ServiceProvider;
use Modules\ActivityLog\Models\ActivityLog;
use Modules\User\Commands\UpdatePermissions;
use Modules\User\GraphQL\Queries\UsersQuery;
use Modules\User\GraphQL\Types\UserType;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\User\Observers\PermissionObserver;
use Modules\User\Observers\RoleObserver;

class UserServiceProvider extends ServiceProvider
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
            'Modules\User');

        $this->registerListeners();
    }

    private function registerListeners()
    {
        \Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            if ($user) {
                $user->last_login_at = Carbon::now();
                $user->login_times++;

                activity('auth')
                    ->causedBy($user)
                    ->withProperties([
                        'login_times' => $user->login_times,
                        'ip'          => request()->getClientIp()
                    ])
                    ->log(ActivityLog::ACTION_LOGIN);

                activity()->disableLogging();
                $user->save();
                activity()->enableLogging();
            }
        });

        \Event::listen(Logout::class, function (Logout $event) {
            $user = $event->user;
            if ($user) {
                activity('auth')
                    ->causedBy($user)
                    ->withProperties([])
                    ->log(ActivityLog::ACTION_LOGOUT);
            }
        });

        Permission::observe(PermissionObserver::class);
        Role::observe(RoleObserver::class);
    }

    private function registerCommands()
    {
        $this->app->singleton('permission.auto-refresh', function ($app) {
            return new UpdatePermissions();
        });

        $this->commands([
            'permission.auto-refresh'
        ]);
    }

    private function registerGraphQL()
    {
        GraphMaker::addSchema([
            'query'    => [
                'users' => UsersQuery::class,
                'user' => UserQuery::class
            ],
            'mutation' => [
                //'updateUserEmail' => 'App\GraphQL\Query\UpdateUserEmailMutation'
            ]
        ]);
    }
}