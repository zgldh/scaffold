<?php namespace Modules\User\Commands;

use Illuminate\Console\Command;
use Modules\User\Models\Permission;
use Modules\User\Repositories\PermissionRepository;
use ReflectionClass;

class UpdatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:auto-refresh {type=api : set guard name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto refresh permissions data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $guardName = $this->argument('type');
        $this->info('Start update permissions...');
        $this->permissions = $this->detectPermissions($guardName);
        $this->table(['name', 'guard_name'], $this->permissions);

//        foreach ($this->connections as $connection) {
//            $this->info('Storing to ' . $connection);
//            $this->processConnection($connection);
//            $this->line("\tComplete.");
//        }

        $connection = 'mysql';
        $this->info('Storing to ' . $connection);
        $this->processConnection($connection);
        $this->call('lang:dump');
        $this->line("\tComplete.");
    }

    private function isNoPermission(\ReflectionMethod $method)
    {
        $doc = $method->getDocComment();
        return str_contains($doc, '@no-permission');
    }

    private function detectPermissions($guardName)
    {
        $controllers = glob(config('scaffold.modules') . '/*/Controllers/*');
        $permissions = [];
        foreach ($controllers as $controller) {
            $controllerClass = str_replace('/', '\\', rtrim($controller, '.php'));
            $controllerName = basename($controller, 'Controller.php');
            if (!class_exists($controllerClass)) {
                continue;
            }
            $reflection = new ReflectionClass($controllerClass);
            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                if ($method->name == '__construct'
                    || !$method->isPublic() || $method->isStatic() || $method->class != $controllerClass
                ) {
                    continue;
                }

                if ($this->isNoPermission($method)) {
                    continue;
                }

                $permissionName = PermissionRepository::GENERATE_PERMISSION_CODE($controllerName, $method->name);
                $permissions[] = $permissionName;
            }
        }

        $repositories = glob(config('scaffold.modules') . '/*/Repositories/*');
        foreach ($repositories as $repository) {
            $repositoryClass = str_replace('/', '\\', rtrim($repository, '.php'));
            $repositoryName = basename($repository, 'Repository.php');
            if (!class_exists($repositoryClass)) {
                continue;
            }
            $reflection = new ReflectionClass($repositoryClass);
            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                if (in_array($method->name, ['__construct', 'model', 'datatables'])
                    || !$method->isPublic() || $method->isStatic() || $method->class != $repositoryClass
                ) {
                    continue;
                }
                if ($this->isNoPermission($method)) {
                    continue;
                }
                $permissionName = PermissionRepository::GENERATE_PERMISSION_CODE($repositoryName, $method->name);
                $permissions[] = $permissionName;
            }
        }

        sort($permissions);
        return array_map(function ($item) use ($guardName) {
            return ['name' => $item, 'guard_name' => $guardName, 'label' => ''];
        }, $permissions);
    }

    private function processConnection($connection)
    {
        $permissionQuery = (new Permission())->setConnection($connection);
        foreach ($this->permissions as $permissionData) {
            $permission = $permissionQuery->firstOrCreate($permissionData);
        }
    }
}
