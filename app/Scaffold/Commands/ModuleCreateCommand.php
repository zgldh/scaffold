<?php namespace App\Scaffold\Commands;

use Illuminate\Console\Command;
use App\Scaffold\Installer\Model\ModelDefinition;
use App\Scaffold\Installer\ModuleStarter;
use App\Scaffold\Installer\Utils;

/**
 * Class ModuleCreateCommand
 * @deprecated
 * @package App\Scaffold\Commands
 */
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
            $this->generateModel();
        }
        foreach ($models as $model) {
            $this->model = $model;
            $this->line('Model: ' . $this->model->getTable());
            $this->generateRepository();
            $this->generateRequest();
            $this->generateController();
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

    /**
     * Get destination path. Will retrieve zgldh-scaffold configuration
     * @param $configPath
     * @param $modelName
     * @return mixed|string
     */
    private function getDestinationPath($configPath, $modelName = null)
    {
        $path = $this->folder . DIRECTORY_SEPARATOR . config('zgldh-scaffold.templates.' . $configPath)[1];
        $path = Utils::fillTemplate(['MODEL_NAME' => $modelName], $path);
        return $path;
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
        $content = Utils::renderTemplate(
            config('zgldh-scaffold.templates.controller', 'zgldh.scaffold::raw.Controller'),
            $variables);

        $destinationPath = $this->getDestinationPath('controller', $pascalCase);
        Utils::writeFile($destinationPath, $content);

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
        $createContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.request.create', 'zgldh.scaffold::raw.Requests.Create'),
            $variables);
        $updateContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.request.update', 'zgldh.scaffold::raw.Requests.Update'),
            $variables);

        $createDestinationPath = $this->getDestinationPath('request.create', $pascalCase);
        $updateDestinationPath = $this->getDestinationPath('request.update', $pascalCase);
        Utils::writeFile($createDestinationPath, $createContent);
        Utils::writeFile($updateDestinationPath, $updateContent);

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
        $content = Utils::renderTemplate(
            config('zgldh-scaffold.templates.repository', 'zgldh.scaffold::raw.Repository'),
            $variables);

        $destinationPath = $this->getDestinationPath('repository', $pascalCase);
        Utils::writeFile($destinationPath, $content);

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
        $content = Utils::renderTemplate(
            config('zgldh-scaffold.templates.model', 'zgldh.scaffold::raw.Model'),
            $variables);
        $destinationPath = $this->getDestinationPath('model', $pascalCase);
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

        $viewContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.resource.views.index', 'zgldh.scaffold::raw.resources.views.index'),
            $variables);
        $viewPath = $this->getDestinationPath('resource.views.index', $pascalCase);
        Utils::writeFile($viewPath, $viewContent);

        $listPageContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.resource.vue.list', 'zgldh.scaffold::raw.resources.assets.ListPage'),
            $variables);
        $editorPageContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.resource.vue.editor', 'zgldh.scaffold::raw.resources.assets.EditorPage'),
            $variables);
        $storeContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.resource.vue.store', 'zgldh.scaffold::raw.resources.assets.store'),
            $variables);
        $listPagePath = $this->getDestinationPath('resource.vue.list', $pascalCase);
        $editorPagePath = $this->getDestinationPath('resource.vue.editor', $pascalCase);
        $storePath = $this->getDestinationPath('resource.vue.store', $pascalCase);
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
        $routesPath = $this->getDestinationPath('resource.routes');
        $routesContent = Utils::renderTemplate(
            config('zgldh-scaffold.templates.resource.routes', 'zgldh.scaffold::raw.resources.assets.routes'),
            $variables);
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
        $enContent = Utils::renderTemplate('zgldh.scaffold::raw.lang.en.t', $variables);
        $zhCnContent = Utils::renderTemplate('zgldh.scaffold::raw.lang.zh-CN.t', $variables);

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
        $content = Utils::renderTemplate(
            config('zgldh-scaffold.templates.routes', 'zgldh.scaffold::raw.routes'),
            $variables);
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
        $content = Utils::renderTemplate(
            config('zgldh-scaffold.templates.menu', 'zgldh.scaffold::raw.menuItem'),
            $variables);
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
        $content = Utils::renderTemplate(
            config('zgldh-scaffold.templates.service_provider', 'zgldh.scaffold::raw.ServiceProvider'),
            $variables);
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