<?php namespace zgldh\Scaffold\Commands;

use Artisan;
use Hamcrest\Util;
use Illuminate\Console\Command;
use InfyOm\Generator\Utils\FileUtil;
use zgldh\Scaffold\Installer\ConfigParser;
use zgldh\Scaffold\Installer\Model\ModelDefinition;
use zgldh\Scaffold\Installer\ModuleStarter;
use zgldh\Scaffold\Installer\Utils;
use zgldh\User\UserCreateCommand;

class ModuleCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zgldh:module:create {starterClass}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create module.';

    private $moduleDirectoryName = null;
    /**
     * @var ModuleStarter
     */
    private $starter = null;
    private $namespace = null;
    private $folder = null;
    /**
     * @var ModelDefinition
     */
    private $model = null;

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
        $starterClass = $this->argument('starterClass');
//        dd($starterClass);
        if (!$starterClass) {
            $this->error("Please use zgldh:module:create Class\To\Starter");
        } elseif (!class_exists($starterClass)) {
            $this->error($starterClass . " doesn't exist");
        } elseif (!$this->isModuleStarter($starterClass)) {
            $this->error($starterClass . " is not a ModuleStarter");
        } else {
            $this->igniteStarter($starterClass);
        }
    }

    private function isModuleStarter($className)
    {
        $ref = new \ReflectionClass($className);
        return $ref->isSubclassOf(ModuleStarter::class);
    }

    /**
     * @param $starterClass
     * @return bool
     */
    private function igniteStarter($starterClass)
    {
        $this->moduleDirectoryName = config('zgldh-scaffold.modules', 'Modules');
        $this->starter = new $starterClass();

        $this->namespace = $this->starter->getModuleNameSpace();
        $this->folder = $this->starter->getModuleFolder();
        $this->info('Generating ' . $this->namespace . ' to ' . $this->folder . '...');

        $models = $this->starter->getModels();
        foreach ($models as $model) {
            $this->model = $model;
            $this->line('Model: ' . $this->model->getTable());
            $this->generateController();
            $this->generateRequests();
//            $this->generateRepository();
        }
        return false;
//
//        $this->addServiceProvider('User', 'UserServiceProvider::class');
//        $this->addRoute('User');
//        $this->addToVueRoute('User');
//        $this->updateAuthConfig();
//        $this->addAdminMenuItem($this->getModuleTemplateContent('menu.blade.php'));
//        $this->publicFactoryAndSeed(
//            $this->getModuleTemplatePath('ModuleUserFactory.php'),
//            $this->getModuleTemplatePath('ModuleUserSeed.php')
//        );
//
//        // Install laravel-permission
//        App::register(PermissionServiceProvider::class);
//        Artisan::call('vendor:publish', [
//            '--provider' => PermissionServiceProvider::class,
//            '--tag'      => 'migrations']);
//        Artisan::call('vendor:publish', [
//            '--provider' => PermissionServiceProvider::class,
//            '--tag'      => 'config']);
//
//        // Publish migrations
//        $this->publishMigration('AddColumnsToUsersTable', __DIR__ . '/../migrations/add_columns_to_users_table.php');
//
//        Artisan::call('migrate');
//
//        $this->createBasicAdmin();
//
//        exec('composer dumpautoload');


        $this->info('Complete.');
    }

    private function generateController()
    {
        $this->comment("\tController...");
        $pascalCase = $this->model->getPascaleCase();
        $template = Utils::template('raw' . DIRECTORY_SEPARATOR . 'Controller.stub');
        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $pascalCase . 'Controller.php';
        $middleware = $this->model->getMiddleware();
        $variables = [
            'NAME_SPACE'                   => $this->namespace,
            'MODEL_NAME'                   => $pascalCase,
            'MIDDLEWARE'                   => $middleware ? '$this->middleware("' . $middleware . '");' : '',
            'USE_CONTROLLER_ACTION_LOG'    => $this->model->isActionLog() ? "use " . $this->moduleDirectoryName . "\ActionLog\Models\ActionLog;" : '',
            'CONTROLLER_INDEX_ACTION_LOG'  => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_SEARCH, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_STORE_ACTION_LOG'  => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_CREATE, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_SHOW_ACTION_LOG'   => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_SHOW, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_UPDATE_ACTION_LOG' => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_UPDATE, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_DELETE_ACTION_LOG' => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_DELETE, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
        ];
        Utils::copy($template, $destinationPath, $variables);
        return;
    }

    private function generateRequests()
    {
        $this->comment("\tRequests...");
        $pascalCase = $this->model->getPascaleCase();
        $variables = [
            'NAME_SPACE' => $this->namespace,
            'MODEL_NAME' => $pascalCase,
            'MODEL'      => $this->model
        ];
        $createContent = Utils::renderTemplate('raw.Requests.Create', $variables);
        $updateContent = Utils::renderTemplate('raw.Requests.Update', $variables);

        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . 'Requests';
        Utils::writeFile($destinationPath . DIRECTORY_SEPARATOR . "Create{$pascalCase}Request.php", $createContent);
        Utils::writeFile($destinationPath . DIRECTORY_SEPARATOR . "Update{$pascalCase}Request.php", $updateContent);

        return;
    }

    private function generateRepository()
    {
        $this->comment("\tRepository...");
        $pascalCase = $this->model->getPascaleCase();
        $controllerTemplate = Utils::template('raw' . DIRECTORY_SEPARATOR . 'Controller.stub');
        $controllerDestPath = $this->folder . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $pascalCase . '.php';
        $middleware = $this->model->getMiddleware();
        $variables = [
            'NAME_SPACE'                   => $this->namespace,
            'MODEL_NAME'                   => $pascalCase,
            'MIDDLEWARE'                   => $middleware ? '$this->middleware("' . $middleware . '");' : '',
            'USE_CONTROLLER_ACTION_LOG'    => $this->model->isActionLog() ? "use " . $this->moduleDirectoryName . "\ActionLog\Models\ActionLog;" : '',
            'CONTROLLER_INDEX_ACTION_LOG'  => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_SEARCH, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_STORE_ACTION_LOG'  => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_CREATE, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_SHOW_ACTION_LOG'   => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_SHOW, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_UPDATE_ACTION_LOG' => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_UPDATE, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
            'CONTROLLER_DELETE_ACTION_LOG' => $this->model->isActionLog() ? 'ActionLog::log(ActionLog::TYPE_DELETE, "' . $this->namespace . '\\' . $pascalCase . '");' : '',
        ];
        Utils::copy($controllerTemplate, $controllerDestPath, $variables);
        return;
    }

    private $dynamicVariables = [];

    private function setupDynamicVariables($folder, $modelName, ConfigParser $config = null)
    {
        $folder = str_replace('\\', '/', $folder);
        $namespace = $config->namespace ?: $this->covertToNameSpace($folder);
        $this->dynamicVariables['PACKAGE_FOLDER'] = $folder;
        $this->dynamicVariables['MODEL_IDENTIFIER'] = strtolower(str_replace('/', '.', $folder));
        $this->dynamicVariables['MODEL_NAME_PRESENTATION'] = $config->name ?: $modelName;
        $this->dynamicVariables['MODEL_NAME'] = $modelName;
        $this->dynamicVariables['MODEL_NAME_LOWER'] = strtolower($this->dynamicVariables['MODEL_NAME']);
        $this->dynamicVariables['MODEL_NAME_PLURAL_LOWER'] = str_plural($this->dynamicVariables['MODEL_NAME_LOWER']);
        $this->dynamicVariables['TABLE_NAME'] = $config->table ?: $this->dynamicVariables['MODEL_NAME_LOWER'];
        $this->dynamicVariables['MIDDLEWARE'] = null;
        $this->dynamicVariables['NAME_SPACE'] = $namespace;
        $this->dynamicVariables['NAME_SPACE_MODEL'] = $this->dynamicVariables['NAME_SPACE'] . '\Models';
        $this->dynamicVariables['NAME_SPACE_REPOSITORY'] = $this->dynamicVariables['NAME_SPACE'] . '\Repositories';
        $this->dynamicVariables['NAME_SPACE_REQUEST'] = $this->dynamicVariables['NAME_SPACE'] . '\Requests';
        $this->dynamicVariables['NAME_SPACE_CONTROLLER'] = $this->dynamicVariables['NAME_SPACE'] . '\Controllers';

        $this->dynamicVariables['APP_PAGE_EMPTY_ITEMS'] = $config->getAppPageEmptyItem();
        $this->dynamicVariables['FORM_FIELDS'] = $config->getFormFields();
        $this->dynamicVariables['DATATABLE_COLUMNS'] = $config->getDatatablesColumns();
    }

    /**
     * 从配置文件生成
     * @param $file
     * @param null $name
     */
    private function fromFieldFile($file, $name = null)
    {
        $config = new ConfigParser($file);
        $folder = dirname($file);

        $name = $name ?: $config->getPackageName();

        $this->setupDynamicVariables($folder, $name, $config);
        $this->dynamicVariables['MIDDLEWARE'] = $config->middleware ?: null;


        $parameters = [
            'model'        => $name,
            '--fieldsFile' => $config->getTempConfigPath(),
            '--tableName'  => $config->table,
            '--quiet'      => true
        ];

        // database migration file
        $createTableName = 'create_' . $config->table . '_table';
        $migrationGeneratorParameters = [
            'name'     => $createTableName,
            '--schema' => $config->getMigrationSchema(),
            '--model'  => false
        ];
        \Artisan::call('make:migration:schema', $migrationGeneratorParameters);

        $this->generateAll($parameters);

        $this->info('All complete');
        $this->info('You can edit migration file to improve the performance.');
    }

    /**
     * 生成 model， repository， request， controller， route， resource， ServiceProvider
     * @param $infyomParameters
     */
    private function generateAll($infyomParameters)
    {
        $folder = $this->dynamicVariables['PACKAGE_FOLDER'];
        $parameters = $infyomParameters;
        // Model
        \Config::set('infyom.laravel_generator.path.model', $folder . '/Models/');
        \Config::set('infyom.laravel_generator.namespace.model', $this->dynamicVariables['NAME_SPACE_MODEL']);
        $this->info('Model...');
        \Artisan::call('infyom:model', $parameters);

        // Repository
        \Config::set('infyom.laravel_generator.path.repository', $folder . '/Repositories/');
        \Config::set('infyom.laravel_generator.namespace.repository', $this->dynamicVariables['NAME_SPACE_REPOSITORY']);
        $this->info('Repository...');
        \Artisan::call('infyom:repository', $parameters);
        $this->adjustRepository();

        // Request
        \Config::set('infyom.laravel_generator.path.request', $folder . '/Requests/');
        \Config::set('infyom.laravel_generator.namespace.request', $this->dynamicVariables['NAME_SPACE_REQUEST']);
        $this->info('Request...');
        \Artisan::call('infyom.scaffold:requests', $parameters);

        $this->generateController($folder . '/Controllers/');
        $this->generateRoute();
        $this->generateResources($folder . '/resources');
        $this->generateServiceProvider($folder . '/');
    }

    private function adjustRepository()
    {
        $repositoryFolder = \Config::get('infyom.laravel_generator.path.repository');
        $repositoryFilePath = $repositoryFolder . $this->dynamicVariables['MODEL_NAME'] . 'Repository.php';
        $content = file_get_contents($repositoryFilePath);
        $content = str_replace('InfyOm\Generator\Common\BaseRepository', 'zgldh\Scaffold\BaseRepository', $content);
        file_put_contents($repositoryFilePath, $content);
    }

    private function covertToNameSpace($str)
    {
        return str_replace('/', '\\', $str);
    }

    /**
     * 生成 routes.php
     */
    private function generateRoute()
    {
        $this->info('Route...');

        // 1. Create routes.php
        $template = file_get_contents(Utils::template('package/routes.stub'));
        $templateData = Utils::fillTemplate($this->dynamicVariables, $template);
        $routesFilePath = $this->dynamicVariables['PACKAGE_FOLDER'] . DIRECTORY_SEPARATOR . 'routes.php';
        file_put_contents($routesFilePath, $templateData);

        // 2. Update /routes/web.php
        $template = file_get_contents(Utils::template('package/routes_web_item.stub'));
        $templateData = Utils::fillTemplate($this->dynamicVariables, $template);
        Utils::addRoute($templateData);
    }

    /**
     * TODO 生成 resources
     * @param $resourceFolder
     */
    private function generateResources($resourceFolder)
    {
        $this->info('Resources...');

        // 1. Copy files
        Utils::copy(Utils::template('package/resources'), $resourceFolder);
        $templateIndex = $resourceFolder . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'index.blade.php';
        $assetAppPage = $resourceFolder . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'AppPage.vue';
        $assetFormFields = $resourceFolder . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'FormFields.html';
        $assetTableColumns = $resourceFolder . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'TableColumns.js';

        // 2. Replace dynamic variables
        Utils::replaceFilePlaceholders($templateIndex, $this->dynamicVariables);
        Utils::replaceFilePlaceholders($assetAppPage, $this->dynamicVariables);
        Utils::replaceFilePlaceholders($assetFormFields, $this->dynamicVariables);
        Utils::replaceFilePlaceholders($assetTableColumns, $this->dynamicVariables);

        // 3. Create entry file
        $entryName = $this->dynamicVariables['MODEL_IDENTIFIER'] . '.js';
        $template = file_get_contents(Utils::template('package/entry.js.stub'));
        $templateData = Utils::fillTemplate($this->dynamicVariables, $template);
        $routesFilePath = resource_path('assets/js/entries/' . $entryName);
        file_put_contents($routesFilePath, $templateData);

        // 4. Add Menu to  resources\views\layouts\menu.blade.php
        $template = file_get_contents(Utils::template('package/menu_item.stub'));
        $templateData = Utils::fillTemplate($this->dynamicVariables, $template);
        Utils::addAdminMenuItem($templateData);
    }

    private function generateServiceProvider($folder)
    {
        $this->info('ServiceProvider...');

        // 1. Create file
        $template = file_get_contents(Utils::template('package/ServiceProvider.stub'));
        $templateData = Utils::fillTemplate($this->dynamicVariables, $template);
        $fileName = $this->dynamicVariables['MODEL_NAME'] . 'ServiceProvider.php';
        FileUtil::createFile($folder, $fileName, $templateData);

        // 2. Add to config/app.php
        $serviceProviderClassName = Utils::fillTemplate($this->dynamicVariables,
            '\\$NAME_SPACE$\$MODEL_NAME$ServiceProvider::class');
        Utils::addServiceProvider($serviceProviderClassName);
    }
}
