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
    protected $signature = 'zgldh:scaffold:init {--name=}';

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
        if (!$this->confirm("Existed files will be replaced. Continue?")) {
            $this->info("Mission abort");
            return false;
        }
        $name = $this->option('name');
        // Setup
        $this->getInput($name);
        $this->setupComposerJson();
        $this->setupPackageJson();
        $this->setupTemplates();
        $this->setupRoute();
        $this->publishAndMigrate();
        $this->setupBasicAdmin();

        $this->info('Scaffold is ready.');
        $this->info('Please run `npm install`.');
    }

    private $dynamicVariables = [];

    private function getInput($name)
    {
        $this->info('Preparing...');

        $packageRoot = $this->ask("Packages root path:", 'packages');
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
        $package->setAutoloadPsr4($this->dynamicVariables['NAME_LOWER'] . "\\\\",
            $this->dynamicVariables['NAME_LOWER'] . "/");
        $package->save();
        $this->info('composer.json saved.');
    }

    private function setupPackageJson()
    {
        $this->info('packages.json...');

        $package = new NpmPackage(base_path('package.json'));
        $package->setScript('dev', 'node build/dev.js');
        $package->setScript('build', 'node build/build.js');

        $package->setDevDependencies("admin-lte", "^2.3.8");
        $package->setDevDependencies("autoprefixer", "^6.5.0");
        $package->setDevDependencies("babel-core", "^6.16.0");
        $package->setDevDependencies("babel-loader", "^6.2.5");
        $package->setDevDependencies("babel-preset-es2015", "^6.16.0");
        $package->setDevDependencies("babel-preset-latest", "^6.24.0");
        $package->setDevDependencies("bootstrap-datepicker", "^1.6.4");
        $package->setDevDependencies("bootstrap-sass", "^3.3.7");
        $package->setDevDependencies("bundle-loader", "^0.5.4");
        $package->setDevDependencies("css-loader", "^0.25.0");
        $package->setDevDependencies("datatables.net", "^1.10.13");
        $package->setDevDependencies("datatables.net-bs", "^1.10.13");
        $package->setDevDependencies("datatables.net-buttons", "^1.2.2");
        $package->setDevDependencies("datatables.net-buttons-bs", "^1.2.2");
        $package->setDevDependencies("datatables.net-select", "^1.2.1");
        $package->setDevDependencies("datatables.net-select-dt", "^1.2.1");
        $package->setDevDependencies("echarts", "^3.4.0");
        $package->setDevDependencies("exports-loader", "^0.6.3");
        $package->setDevDependencies("file-loader", "^0.9.0");
        $package->setDevDependencies("font-awesome", "^4.7.0");
        $package->setDevDependencies("glob", "^7.1.0");
        $package->setDevDependencies("html-loader", "^0.4.4");
        $package->setDevDependencies("i", "^0.3.5");
        $package->setDevDependencies("icheck", "^1.0.2");
        $package->setDevDependencies("imports-loader", "^0.6.5");
        $package->setDevDependencies("ionicons", "^3.0.0");
        $package->setDevDependencies("jquery", "^2.0.0");
        $package->setDevDependencies("lodash", "^4.16.2");
        $package->setDevDependencies("node-notifier", "^4.6.1");
        $package->setDevDependencies("npm", "^3.10.9");
        $package->setDevDependencies("node-sass", "^4.0.0");
        $package->setDevDependencies("nprogress", "^0.2.0");
        $package->setDevDependencies("promise", "^7.1.1");
        $package->setDevDependencies("sass-loader", "^4.0.2");
        $package->setDevDependencies("select2", "^4.0.0");
        $package->setDevDependencies("shelljs", "^0.7.4");
        $package->setDevDependencies("style-loader", "^0.13.1");
        $package->setDevDependencies("sweetalert2", "^6.1.1");
        $package->setDevDependencies("tinymce", "^4.4.3");
        $package->setDevDependencies("toastr", "^2.1.2");
        $package->setDevDependencies("url-loader", "^0.5.7");
        $package->setDevDependencies("vue-loader", "^10.0.0");
        $package->setDevDependencies("vue-resource", "^1.2.1");
        $package->setDevDependencies("vue-router", "^2.1.1");
        $package->setDevDependencies("vue-template-compiler", "^2.1.0");
        $package->setDevDependencies("vuex", "^2.0.0");
        $package->setDevDependencies("webpack", "^2.1.0-beta.28");
        $package->setDevDependencies("webpack-dev-server", "^1.16.2");

        $package->setDependencies("babel-plugin-transform-runtime", "^6.23.0");
        $package->setDependencies("babel-polyfill", "^6.16.0");
        $package->setDependencies("babel-preset-es2015", "^6.24.0");
        $package->setDependencies("babel-preset-stage-2", "^6.22.0");
        $package->setDependencies("element-ui", "^1.2.8");
        $package->setDependencies("little-loader", "^0.1.1");
        $package->setDependencies("vue", "^2.1.0");
        $package->setDependencies("vue-select", "^2.2.0");

        $package->save();
        $this->info('package.json saved.');
    }

    private function setupTemplates()
    {
        $this->info('Templates and facilities...');

        Utils::copy(Utils::template('init/app'), base_path('app'));
        Utils::copy(Utils::template('init/database'), base_path('database'));
        Utils::copy(Utils::template('init/build'), base_path('build'));
        Utils::copy(Utils::template('init/resources'), base_path('resources'));

        // User package JS files
        Utils::replaceFilePlaceholders(base_path('resources/assets/js/entries/user.list.js'), $this->dynamicVariables);
        Utils::replaceFilePlaceholders(base_path('resources/assets/js/entries/user.role.js'), $this->dynamicVariables);
        Utils::replaceFilePlaceholders(base_path('resources/assets/js/entries/user.permission.js'),
            $this->dynamicVariables);
    }

    private function setupRoute()
    {
        $this->info('Routes...');

        Utils::copy(Utils::template('init/routes/web.php'), base_path('routes/web.php'));
        $this->comment("Routes added.");
    }

    private function publishAndMigrate()
    {
        Artisan::call('vendor:publish');
        Artisan::call('migrate');
        $this->comment("Vendor published and migrated.");
    }

    private function setupBasicAdmin()
    {
        $command = new UserCreateCommand();
        $command->createBasicAdmin();
        $this->comment("Admin added.");
    }
}
