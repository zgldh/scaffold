<?php namespace Modules\Upload\Commands;

use Illuminate\Console\Command;
use Modules\User\Models\Permission;
use Modules\User\Repositories\PermissionRepository;
use ReflectionClass;

class CleanUnUsedUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:clean {user-id=} {type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete these un-used uploads';

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
}
