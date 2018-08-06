<?php namespace Modules\Upload\Commands;

use Illuminate\Console\Command;
use Modules\User\Models\Permission;
use Modules\User\Repositories\PermissionRepository;
use ReflectionClass;
use zgldh\UploadManager\UploadManager;

class CleanUnUsedUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:clean {--user-id=} {--type=}';

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
        $userId = $this->option('user-id') ?: null;
        $type = $this->option('type') ?: null;

        $uploadManager = UploadManager::getInstance();
        $uploadManager->removeUnUsedUploads($userId, $type);


        $this->info('Start cleaning un-used uploads...');
        $this->line("\tComplete.");
    }
}
