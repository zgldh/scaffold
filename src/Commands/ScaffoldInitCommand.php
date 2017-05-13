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
    protected $signature = 'zgldh:scaffold:init {name=Modules} {--setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initalize scaffold, publish HTML, VueJS and templates.';

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
        $setup = $this->option('setup');
        if (!$setup) {
            if (!$this->confirm("Existed files will be replaced. Continue?")) {
                $this->info("Mission abort");
                return false;
            }
            $name = $this->argument('name');
            // Setup
            $this->getInput($name);
            $this->setupComposerJson();
            $this->setupPackageJson();
            $this->setupTemplates();
            $this->setupRoute();
            $this->setUserModule();
            $this->info('Scaffold is initialized. Please run `php artisan zgldh:scaffold:init --setup`');
        } else {
            $this->publishAndMigrate();
            $this->setupAdminAccount();
            $this->info('Scaffold is ready. Please run `yarn run hot` to start.');
        }

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

    private function setupComposerJson()
    {
        $this->info('composer.json...');

        $package = new ComposerParser(base_path('composer.json'));
        $package->setAutoloadPsr4($this->dynamicVariables['NAME'] . "\\",
            $this->dynamicVariables['NAME'] . "/");

        $package->save();
        $this->info('composer.json saved.');

        Utils::addServiceProvider($this->dynamicVariables['NAME'] . '\User\UserServiceProvider::class');
        Utils::addServiceProvider('zgldh\UploadManager\UploadManagerServiceProvider::class');
        Utils::addServiceProvider('GrahamCampbell\Exceptions\ExceptionsServiceProvider::class');
        Utils::addServiceProvider('Prettus\Repository\Providers\RepositoryServiceProvider::class');
        Utils::addServiceProvider('InfyOm\Generator\InfyOmGeneratorServiceProvider::class');
        Utils::addServiceProvider('Yajra\Datatables\DatatablesServiceProvider::class');
        Utils::addServiceProvider('Spatie\Permission\PermissionServiceProvider::class');
        Utils::addServiceProvider('Laravel\Passport\PassportServiceProvider::class');
    }

    private function setupPackageJson()
    {
        $this->info('packages.json...');

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
        $this->info('package.json saved.');

        exec("yarn");
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
