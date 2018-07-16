<?php namespace App\Scaffold\Commands;

use Artisan;
use Hamcrest\Util;
use Illuminate\Console\Command;
use zgldh\Scaffold\Installer\ComposerParser;
use zgldh\Scaffold\Installer\KernelEditor;
use zgldh\Scaffold\Installer\NpmPackageParser;
use zgldh\Scaffold\Installer\Utils;

class ScaffoldInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize scaffold.';

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
        $this->info('Scaffold running...');
        $this->call('migrate');
        try {
            $this->call('storage:link');
        } catch (\Exception $e) {
            //
        }
        $this->call('permission:auto-refresh');
        $this->call('db:seed', ['--class' => 'ScaffoldInitialSeeder']);
        $this->call('lang:dump');
        $this->info('Complete');
    }
}
