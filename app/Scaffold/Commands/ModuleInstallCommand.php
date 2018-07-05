<?php namespace App\Scaffold\Commands;

use Artisan;
use Illuminate\Console\Command;
use InfyOm\Generator\Utils\FileUtil;
use zgldh\Scaffold\Installer\ConfigParser;
use zgldh\Scaffold\Installer\Utils;
use zgldh\User\UserCreateCommand;

/**
 * Class ModuleInstallCommand
 * @deprecated
 * @package App\Scaffold\Commands
 */
class ModuleInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zgldh:module:install {moduleNameSpace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create package.';

    /**
     * Create a new command instance.
     *
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
        $moduleNameSpace = $this->argument('moduleNameSpace');
        $moduleNameSpace = str_replace('/', '\\', camel_case($moduleNameSpace));
        $moduleNameSpace = str_replace('\module', '\Module', $moduleNameSpace);
        $moduleInstallerClass = $moduleNameSpace . '\\ModuleInstaller';
        if (class_exists($moduleInstallerClass)) {
            $installer = new $moduleInstallerClass;
        } else {
            $this->error($moduleInstallerClass . ' is not found.');
            return;
        }
        $installer->run();
        $this->info($moduleNameSpace . ' installation complete.');
    }
}
