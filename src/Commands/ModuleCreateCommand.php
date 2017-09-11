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
    protected $signature = 'zgldh:module:create {starterClass} {--only=*}';

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

    private $onlyGenerators = [];

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
        $starterClass = str_replace('/', '\\', $starterClass) . '\\Starter';

        $this->onlyGenerators = $this->option('only');
        foreach ($this->onlyGenerators as $key => $only) {
            $this->onlyGenerators[$key] = snake_case($only);
        }

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

    private function checkWilling($key)
    {
        $key = snake_case($key);
        if (count($this->onlyGenerators) > 0) {
            if (in_array($key, $this->onlyGenerators)) {
                return true;
            }
            return false;
        } else {
            return true;
        }
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
            $this->generateRequest();
            $this->generateRepository();
            $this->generateModel();
            $this->generateMigration();
            $this->generateResource();
        }
        $this->generateResourceRoutes();
        $this->generateLanguageFiles();
        $this->generateRoutes();
        $this->generateMenu();
        $this->generateServiceProvider();

//        Temporary disable code format feature.
//        $this->codeFormat();

        system('composer dumpautoload');

        return false;
    }

    private function generateController()
    {
        $this->comment("\tController...");
        if (!$this->checkWilling('controller')) {
            return $this->line("\tskip");
        }
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

    private function generateRequest()
    {
        $this->comment("\tRequest...");
        if (!$this->checkWilling('request')) {
            return $this->line("\tskip");
        }
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
        if (!$this->checkWilling('repository')) {
            return $this->line("\tskip");
        }
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
        if (!$this->checkWilling('model')) {
            return $this->line("\tskip");
        }
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
        if (!$this->checkWilling('migration')) {
            return $this->line("\tskip");
        }

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
     * 生成 resources
     */
    private function generateResource()
    {
        $this->comment("\tResource...");
        if (!$this->checkWilling('resource')) {
            return $this->line("\tskip");
        }

        $pascalCase = $this->model->getPascaleCase();
        $variables = [
            'NAME_SPACE' => $this->namespace,
            'STARTER'    => $this->starter,
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
        $storeContent = Utils::renderTemplate('raw.resources.assets.store', $variables);
        $listPagePath = $assetsFolder . "ListPage.vue";
        $editorPagePath = $assetsFolder . "EditorPage.vue";
        $storePath = $assetsFolder . "store.js";
        Utils::writeFile($listPagePath, $listPageContent);
        Utils::writeFile($editorPagePath, $editorPageContent);
        Utils::writeFile($storePath, $storeContent);
    }

    /**
     * 生成 vue admin routes
     */
    private function generateResourceRoutes()
    {
        $this->comment("Resources routes...");
        if (!$this->checkWilling('resource')) {
            return $this->line("\tskip");
        }

        $variables = [
            'STARTER' => $this->starter,
        ];
        $resourceFolder = $this->folder . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        $routesPath = $resourceFolder . "assets" . DIRECTORY_SEPARATOR . "routes.js";
        $routesContent = Utils::renderTemplate('raw.resources.assets.routes', $variables);
        Utils::writeFile($routesPath, $routesContent);

        $routeLine = "require('{$this->moduleDirectoryName}/{$this->starter->getModuleName()}/resources/assets/routes.js').default";
        Utils::addToVueRoute($routeLine);
    }

    private function generateLanguageFiles()
    {
        $this->comment("Language Files...");
        if (!$this->checkWilling('language')) {
            return $this->line("\tskip");
        }

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
        $this->comment("Route...");
        if (!$this->checkWilling('route')) {
            return $this->line("\tskip");
        }

        $variables = [
            'STARTER' => $this->starter,
        ];
        $content = Utils::renderTemplate('raw.routes', $variables);
        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . "routes.php";
        Utils::writeFile($destinationPath, $content);

        Utils::addRoute("require base_path('{$this->namespace}/routes.php');");

        return;
    }

    /**
     * 生成 menu 到 resources\views\admin\menu.blade.php
     */
    private function generateMenu()
    {
        $this->comment("Menu...");
        if (!$this->checkWilling('menu')) {
            return $this->line("\tskip");
        }

        $variables = [
            'STARTER' => $this->starter,
        ];
        $content = Utils::renderTemplate('raw.menuItem', $variables);
        Utils::addAdminMenuItem($content);
        return;
    }

    private function generateServiceProvider()
    {
        $this->comment("Service Provider...");
        if (!$this->checkWilling('service-provider')) {
            return $this->line("\tskip");
        }

        // 1. Create file
        $moduleName = $this->starter->getModuleName();
        $variables = [
            'NAME_SPACE'  => $this->namespace,
            'STARTER'     => $this->starter,
            'MODULE_NAME' => $moduleName,
        ];
        $content = Utils::renderTemplate('raw.ServiceProvider', $variables);
        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . "{$moduleName}ServiceProvider.php";
        Utils::writeFile($destinationPath, $content);

        // 2. Add to config/app.php
        Utils::addServiceProvider("{$this->namespace}\\{$moduleName}ServiceProvider::class");
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