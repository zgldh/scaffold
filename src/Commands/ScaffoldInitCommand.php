<?php namespace zgldh\Scaffold\Commands;

use Artisan;
use Hamcrest\Util;
use Illuminate\Console\Command;
use zgldh\Scaffold\Installer\ComposerParser;
use zgldh\Scaffold\Installer\KernelEditor;
use zgldh\Scaffold\Installer\NpmPackageParser;
use zgldh\Scaffold\Installer\Utils;

class ScaffoldInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zgldh:scaffold:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initalize scaffold, publish HTML, VueJS and templates.';

    private $moduleDirectoryName = 'Modules';
    private $host = 'localhost';

    private $dynamicVariables = [];

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
        $this->moduleDirectoryName = $this->ask("What's the Module directory name? e.g. MyModules", 'Modules');
        $this->host = $this->ask("What's the local host name of this project? e.g. my-project.local", 'localhost');

        $this->dynamicVariables['NAME'] = $this->moduleDirectoryName;
        $this->dynamicVariables['HOST'] = $this->host;

        //    1. 创建 Modules 目录
        $this->createModulesDirectory();
        //    2. 自动设置好根目录的 `composer.json`
        $this->setupComposerJson();
        //    3. 自动设置好根目录的 `packages.json`
        $this->setupPackageJson();
        //    4. 自动设置好根目录的 `webpack.mix.js`
        $this->setupWebpackMixJs();
        //    5. 自动设置好 `/config/zgldh-scaffold.php` 里面会储存 Modules 目录名
        $this->setupConfiguration();
        //    6. 自动设置好 `/app`, `/resources`, `/routes`
        $this->setupFiles();
        //    7. 初始的几个Modules： Dashboard 等
        $this->setupModules();
        //    8. 自动执行 `composer dumpautoload`
        $this->composerDumpAutoload();
        //    9. 自动执行 `php artisan vendor:publish`, `php artisan migrate`
        $this->publishVendors();

        $this->info('Scaffold is ready. Please run following commands:');
        $this->line('npm install');
        $this->line('gulp watch');
        $this->line('Go to browser for: http://' . $this->host . ' to start development.');
    }

    private function createModulesDirectory()
    {
        $moduleDirectory = base_path($this->moduleDirectoryName);
        $this->line("Creating modules directory to " . $moduleDirectory);
        if (!is_dir($moduleDirectory)) {
            mkdir($moduleDirectory, 0755, true);
        }
        $this->info('Complete!');
    }

    private function setupComposerJson()
    {
        $this->line('Setting up composer.json...');

        $package = new ComposerParser(base_path('composer.json'));
        $package->setAutoloadPsr4($this->moduleDirectoryName . "\\",
            $this->moduleDirectoryName . "/");

        $package->save();
        $this->info('Complete!');
    }

    private function setupPackageJson()
    {
        $this->line('Setting up packages.json...');

        $package = new NpmPackageParser(base_path('package.json'));

        $package->setDevDependencies("materialize-css", "~0.98");
        $package->setDevDependencies("nprogress", "~0.2");
        $package->setDevDependencies("font-awesome", "~4.7");
        $package->setDevDependencies("ionicons", "~3.0");
        $package->setDevDependencies("promise", "~7.1.1");
        $package->setDevDependencies("tinymce", "~4.4.3");
        $package->setDevDependencies("vue-router", "~2.3");
        $package->setDevDependencies("vuex", "~2.2");

        $package->setDependencies("babel-polyfill", "^6.23.0");
        $package->setDependencies("little-loader", "^0.1.1");
        $package->setDependencies("element-ui", "~1.3");

        $package->save();
        $this->info('Complete!');
    }

    /**
     *4. 自动设置好根目录的 `webpack.mix.js`
     */
    private function setupWebpackMixJs()
    {
        $this->line('Setting up webpack.mix.js...');
        Utils::replaceFilePlaceholders(Utils::template('init/webpack.mix.js'), $this->dynamicVariables,
            base_path('webpack.mix.js'));
        $this->info('Complete!');
    }

    private function setupConfiguration()
    {
        $this->line('Setting up configurations...');

        Utils::replaceFilePlaceholders(Utils::template('init/config/zgldh-scaffold.php'), $this->dynamicVariables,
            base_path('config/zgldh-scaffold.php'));

        Utils::addServiceProvider('Laravel\Passport\PassportServiceProvider::class');
        Utils::addServiceProvider('GrahamCampbell\Exceptions\ExceptionsServiceProvider::class');
        Utils::addServiceProvider('Prettus\Repository\Providers\RepositoryServiceProvider::class');
        Utils::addServiceProvider('Yajra\Datatables\DatatablesServiceProvider::class');
        Utils::addServiceProvider('Clockwork\Support\Laravel\ClockworkServiceProvider::class');

        KernelEditor::addMiddleware('\Clockwork\Support\Laravel\ClockworkMiddleware::class');

        $this->info('Complete!');
    }

    private function setupFiles()
    {
        $this->line('Setting up files...');

        Utils::copy(Utils::template('init/app'), base_path('app'));
        Utils::copy(Utils::template('init/resources'), base_path('resources'));
        Utils::copy(Utils::template('init/routes'), base_path('routes'));

        KernelEditor::addMiddleware('\App\Http\Middleware\MultipartFormDataParser::class');
        Utils::replaceFilePlaceholders(resource_path('assets/js/entries/admin.js'), $this->dynamicVariables);

        \Artisan::call('storage:link');

        $this->info('Complete!');
    }

    private function setupModules()
    {
        $this->line('Setting up initialize Modules...');

        // Dashboard
        Utils::copy(Utils::template('modules/Dashboard'), base_path($this->moduleDirectoryName . '/Dashboard'),
            $this->dynamicVariables);
        Utils::addServiceProvider($this->moduleDirectoryName . '\Dashboard\DashboardServiceProvider::class');
        Utils::addRoute("require base_path('{$this->moduleDirectoryName}/Dashboard/routes.php');");
        Utils::addToVueRoute("require('{$this->moduleDirectoryName}/Dashboard/resources/assets/routes.js').default;");

        $this->info('Complete!');
    }

    private function composerDumpAutoload()
    {
        $this->line('Composer dumpautoload');
        exec('composer dumpautoload');
        $this->info('Complete!');
    }

    private function publishVendors()
    {
        $this->line('Publishing vendors');
        exec('php artisan vendor:publish');
        exec('php artisan migrate');
        $this->info('Complete!');
    }

}
