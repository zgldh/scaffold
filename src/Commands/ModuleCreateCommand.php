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
        $starterClass = str_replace('/', '\\', $starterClass);

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
            $this->generateRepository();
            $this->generateModel();
            $this->generateMigration();
            $this->generateResources();
        }
        $this->generateResourceRoutes();
        $this->generateLanguageFiles();
        $this->generateRoutes();
        $this->generateServiceProvider();

        $this->codeFormat();

        system('composer dumpautoload');

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
        $variables = [
            'MODULE_DIRECTORY_NAME' => $this->moduleDirectoryName,
            'NAME_SPACE'            => $this->namespace,
            'MODEL_NAME'            => $pascalCase,
            'MODEL'                 => $this->model
        ];
        $content = Utils::renderTemplate('raw.Controller', $variables);

        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . 'Controllers';
        Utils::writeFile($destinationPath . DIRECTORY_SEPARATOR . "{$pascalCase}Controller.php", $content);

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
        $variables = [
            'NAME_SPACE' => $this->namespace,
            'MODEL_NAME' => $pascalCase,
            'MODEL'      => $this->model
        ];
        $content = Utils::renderTemplate('raw.Repository', $variables);

        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . 'Repositories';
        Utils::writeFile($destinationPath . DIRECTORY_SEPARATOR . "{$pascalCase}Repository.php", $content);

        return;
    }

    private function generateModel()
    {
        $this->comment("\tModel...");
        $pascalCase = $this->model->getPascaleCase();
        $variables = [
            'NAME_SPACE' => $this->namespace,
            'MODEL_NAME' => $pascalCase,
            'MODEL'      => $this->model
        ];
        $content = Utils::renderTemplate('raw.Model', $variables);

        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . "{$pascalCase}.php";
        Utils::writeFile($destinationPath, $content);

        return;
    }


    private function generateMigration()
    {
        $this->comment("\tMigration File...");

        $create = "create_{$this->model->getTable()}_table";
        if ($filePath = Utils::isMigrationFileExists($create)) {
            $this->warn("\t! There is already a migration file " . $filePath . ' !');
            $confirm = $this->confirm("\tYES to generate anyway; NO to skip. No matter your choice, the old migration file will remain in place.");
            if (!$confirm) {
                $this->info("\tNo migration generated.");
                return false;
            }
            $this->info("\tKeep going to generate...");
        }
        $command = "make:migration:schema";
        $schema = $this->model->getTableSchema();

        \Artisan::call($command, [
            'name'     => $create,
            '--schema' => $schema,
            '--model'  => false
        ]);
        return;
    }


    /**
     * TODO 生成 resources
     * @param $resourceFolder
     */
    private function generateResources()
    {
        $this->comment("\tResources...");
        $pascalCase = $this->model->getPascaleCase();
        $variables = [
            'NAME_SPACE' => $this->namespace,
            'MODEL_NAME' => $pascalCase,
            'MODEL'      => $this->model
        ];

        $resourceFolder = $this->folder . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        $viewFolder = $resourceFolder . "views" . DIRECTORY_SEPARATOR . $pascalCase . DIRECTORY_SEPARATOR;
        $assetsFolder = $resourceFolder . "assets" . DIRECTORY_SEPARATOR . $pascalCase . DIRECTORY_SEPARATOR;

        $viewContent = Utils::renderTemplate('raw.resources.views.index', $variables);
        $viewPath = $viewFolder . "index.blade.php";
        Utils::writeFile($viewPath, $viewContent);

        $listPageContent = Utils::renderTemplate('raw.resources.assets.ListPage', $variables);
        $editorPageContent = Utils::renderTemplate('raw.resources.assets.EditorPage', $variables);
        $listPagePath = $assetsFolder . "ListPage.vue";
        $editorPagePath = $assetsFolder . "EditorPage.vue";
        Utils::writeFile($listPagePath, $listPageContent);
        Utils::writeFile($editorPagePath, $editorPageContent);
    }

    /**
     * TODO 生成 vue admin routes
     */
    private function generateResourceRoutes()
    {
        return false;
        $this->comment("\tResources routes...");
        $pascalCase = $this->model->getPascaleCase();
        $variables = [
            'NAME_SPACE' => $this->namespace,
            'MODEL_NAME' => $pascalCase,
            'MODEL'      => $this->model
        ];

        $resourceFolder = $this->folder . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        $viewFolder = $resourceFolder . "views" . DIRECTORY_SEPARATOR . $pascalCase . DIRECTORY_SEPARATOR;

        $routesContent = Utils::renderTemplate('raw.resources.assets.routes', $variables);
        $routesPath = $viewFolder . "routes.js";
        Utils::writeFile($routesContent, $routesPath);
    }

    private function generateLanguageFiles()
    {
        $this->comment("\tLanguage Files...");

        $variables = [
            'STARTER' => $this->starter,
        ];
        $enContent = Utils::renderTemplate('raw.lang.en.t', $variables);
        $zhCnContent = Utils::renderTemplate('raw.lang.zh-CN.t', $variables);

        $moduleSnakeCase = snake_case($this->starter->getModuleName());
        $folder = resource_path('lang/vendor/' . $moduleSnakeCase);
        Utils::writeFile($folder . '/en/t.php', $enContent);
        Utils::writeFile($folder . '/zh-CN/t.php', $zhCnContent);

    }

    /**
     * 生成 routes.php
     */
    private function generateRoutes()
    {
        $this->info('Route...');

        $this->comment("\tModel...");
        $variables = [
            'STARTER' => $this->starter,
        ];
        $content = Utils::renderTemplate('raw.routes', $variables);
        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . "routes.php";
        Utils::writeFile($destinationPath, $content);

        Utils::addRoute("require base_path('{$this->namespace}/routes.php');");

        return;
    }

    private function generateServiceProvider()
    {
        $this->comment("\tService Provider...");

        // 1. Create file
        $moduleName = $this->starter->getModuleName();
        $variables = [
            'NAME_SPACE'  => $this->namespace,
            'MODULE_NAME' => $moduleName,
        ];
        $content = Utils::renderTemplate('raw.ServiceProvider', $variables);
        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . "{$moduleName}ServiceProvider.php";
        Utils::writeFile($destinationPath, $content);

        // 2. Add to config/app.php
        Utils::addServiceProvider("{$this->namespace}\\{$moduleName}ServiceProvider::class");
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
        $this->generateRoutes();
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

    private function codeFormat()
    {
        $this->comment("Code formatting...");
        $rules = include_once(__DIR__ . '/../Installer/CodeFormatRules.php');
        $command = 'php ' . base_path('vendor/friendsofphp/php-cs-fixer/php-cs-fixer') . ' fix ';
        $command .= $this->folder;
        $command .= ' --using-cache=no';
        $command .= ' --rules="' . str_replace('"', '\"', json_encode($rules)) . '"';

        system($command);
    }
}