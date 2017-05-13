<?php namespace zgldh\Scaffold\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zgldh:user:create {--base-admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user, default password is 123456.';

    private $userClass = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->userClass = config('auth.providers.users.model');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('base-admin')) {
            return $this->createBasicAdmin();
        }
        //
        $name = $this->ask('user name? (for login)');
        $userClass = app(config('auth.providers.users.model'));
        while ($userClass->where('name', $name)->count() > 0) {
            $name = $this->ask($name . ' is existed, please reenter a name.');
        }
        $email = $this->ask('email?', 'none');

        $user = new $this->userClass;
        $user->name = $name;
        $user->email = $email == 'none' ? '' : $email;
        $user->password = bcrypt('123456');
        $user->save();

        $role = $this->ask('role?', 'none');
        if ($role !== 'none') {
            $role = Role::firstOrCreate(['name' => $role]);
            $user->assignRole($role);
        }
        $this->info($name . ' has been created.');

    }

    public function createBasicAdmin()
    {
        $user = call_user_func_array($this->userClass . '::firstOrNew', [['name' => 'admin']]);
        $user->email = 'admin@email.com';
        $user->password = bcrypt('123456');
        $user->save();

        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($role);

        $permission = Permission::firstOrCreate(['name' => 'can-manage-user']);
        $role->givePermissionTo($permission);
    }
}
