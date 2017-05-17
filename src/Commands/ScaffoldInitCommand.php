<?php namespace zgldh\Scaffold\Commands;

use Artisan;
use Illuminate\Console\Command;
use zgldh\Scaffold\ComposerParser;
use zgldh\Scaffold\NpmPackage;
use zgldh\Scaffold\Utils;

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
        //    6. 自动设置好 `/resources`
        $this->setupResources();
        //    7. 自动执行 `composer dumpautoload`
        $this->composerDumpAutoload();

        $this->info('Scaffold is ready. Please run following commands:');
        $this->line('npm install');
        $this->line('php artisan migrate');
        $this->line('gulp watch');
        $this->line('Go to browser for: http://' . $this->host . ' to start development.');
    }

    private function createModulesDirectory()
    {
        $moduleDirectory = base_path($this->moduleDirectoryName);
        $this->line("Creating modules directory to " . $moduleDirectory);
        mkdir($moduleDirectory, 0755, true);
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

        $package = new NpmPackage(base_path('package.json'));

        $package->setDevDependencies("nprogress", "^0.2.0");
        $package->setDevDependencies("font-awesome", "^4.7.0");
        $package->setDevDependencies("ionicons", "^3.0.0");
        $package->setDevDependencies("promise", "^7.1.1");
        $package->setDevDependencies("tinymce", "^4.4.3");
        $package->setDevDependencies("vue-router", "^2.3.0");
        $package->setDevDependencies("vuex", "^2.2.0");

        $package->setDependencies("little-loader", "^0.1.1");
        $package->setDependencies("element-ui", "^1.2.4");

        $package->save();
        $this->info('Complete!');
    }

    private function setupWebpackMixJs()
    {
        $this->line('Setting up webpack.mix.js...');
        //TODO         //    4. 自动设置好根目录的 `webpack.mix.js`
        $this->info('Complete!');
    }

    private function setupConfiguration()
    {
        $this->line('Setting up configurations...');

        // TODO     5. 自动设置好 `/config/zgldh-scaffold.php` 里面会储存 Modules 目录名

        Utils::addServiceProvider('Laravel\Passport\PassportServiceProvider::class');
        Utils::addServiceProvider('GrahamCampbell\Exceptions\ExceptionsServiceProvider::class');
        Utils::addServiceProvider('Prettus\Repository\Providers\RepositoryServiceProvider::class');
        Utils::addServiceProvider('Yajra\Datatables\DatatablesServiceProvider::class');
        Utils::addServiceProvider('Clockwork\Support\Laravel\ClockworkServiceProvider::class');

        $this->info('Complete!');
    }

    private function setupResources()
    {
        $this->line('Setting up resources...');
        //TODO         //    6. 自动设置好 `/resources`
        $this->info('Complete!');
    }


    private function composerDumpAutoload()
    {
        $this->line('Composer dumpautoload');
        exec('composer dumpautoload');
        $this->info('Complete!');
    }

    private $dynamicVariables = [];

    private function getInput($name)
    {
        $this->info('Preparing...');

        $packageRoot = $this->ask("Packages root path:", $name);
        $namespace = trim($packageRoot, '/');
        $packageRoot = base_path($packageRoot);
        $this->dynamicVariables['NAME'] = $name;
        $this->dynamicVariables['NAME_LOWER'] = strtolower($this->dynamicVariables['NAME']);
        $this->dynamicVariables['PACKAGES_PATH'] = $packageRoot;
        $this->dynamicVariables['PACKAGE_USER_PATH'] = $namespace . '/User';
        $this->dynamicVariables['NAME_SPACE_USER'] = $namespace . '\User';
    }


    private function setupTemplates()
    {
        $this->info('Templates and facilities...');

        Utils::copy(Utils::template('init/webpack.mix.js'), base_path('webpack.mix.js'));
        Utils::copy(Utils::template('init/app'), base_path('app'));
        Utils::copy(Utils::template('init/database'), base_path('database'));
        Utils::copy(Utils::template('init/resources'), base_path('resources'));

        $originalAppJsPath = resource_path('assets/js/app.js');
        $this->info($originalAppJsPath . ' is useless. You can remove that file.');
//        unlink(resource_path('assets/js/app.js')); // The original app.js is useless

        // User package JS files
        Utils::replaceFilePlaceholders(base_path('webpack.mix.js'), $this->dynamicVariables);
        Utils::replaceFilePlaceholders(app_path('Providers/AuthServiceProvider.php'), [
            '$this->registerPolicies();' => '$this->registerPolicies();' . "\n" . '        \Laravel\Passport\Passport::routes();',
            "'driver' => 'token'"        => "'driver' => 'passport'"
        ], null, '');
//        Utils::replaceFilePlaceholders(base_path('resources/assets/js/entries/user.role.js'), $this->dynamicVariables);
//        Utils::replaceFilePlaceholders(base_path('resources/assets/js/entries/user.permission.js'),
//            $this->dynamicVariables);
    }

    private function setupRoute()
    {
        $this->info('Routes...');

        Utils::addRoute("Route::get('/admin', function () {return view('admin');});");

//        Utils::copy(Utils::template('init/routes/web.php'), base_path('routes/web.php'));
        $this->comment("Routes added.");
    }

    private function setUserModule()
    {
        $this->info('user Module...');

        Utils::copy(Utils::template('user'), $this->dynamicVariables['PACKAGE_USER_PATH']);
        foreach (glob($this->dynamicVariables['PACKAGE_USER_PATH'] . '/*/*.php') as $file) {
            Utils::replaceFilePlaceholders($file, $this->dynamicVariables);
        }
        Utils::replaceFilePlaceholders($this->dynamicVariables['PACKAGE_USER_PATH'] . '/UserServiceProvider.php',
            $this->dynamicVariables);
        Utils::replaceFilePlaceholders($this->dynamicVariables['PACKAGE_USER_PATH'] . '/routes.php',
            $this->dynamicVariables);

        $userModelClass = '\\' . $this->dynamicVariables['NAME'] . '\User\Models\User::class';
        Utils::replaceFilePlaceholders(config_path('auth.php'),
            ['App\User::class' => $userModelClass],
            null, '');

        config(['auth.providers.users.model' => $userModelClass]);

        exec('composer dumpautoload');

        $this->info('user Module set.');
    }

    private function publishAndMigrate()
    {
        $this->comment("publishing...");
        Artisan::call('vendor:publish');
        $this->comment('migrating...');
        Artisan::call('migrate');
        Artisan::call('make:auth');
        Artisan::call('passport:install');
        Artisan::call('vendor:publish --tag=passport-components');
        $this->comment("Vendor published and migrated.");
    }

    private function setupAdminAccount()
    {
        $this->comment("Preparing admin account...");
        Artisan::call('zgldh:user:create', ['--base-admin' => true]);
        $this->comment("Admin account is ready.");

    }

}
