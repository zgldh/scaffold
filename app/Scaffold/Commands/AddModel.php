<?php namespace App\Scaffold\Commands;

use App\Scaffold\Installer\ModelStarter;
use Illuminate\Console\Command;
use App\Scaffold\Installer\Model\ModelDefinition;
use App\Scaffold\Installer\Utils;
use Modules\User\Repositories\PermissionRepository;

class AddModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:model {modelStarterClass} {--only=*} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create controller/model/repository/requests by given model starter.';

    private $moduleDirectoryName = null;
    /**
     * @var ModelStarter
     */
    private $starter = null;
    private $namespace = null;
    private $folder = null;
    /**
     * @var ModelDefinition
     */
    private $model = null;

    private $onlyGenerators = [];
    private $isForce = false;

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
        $this->normalizeGeneratorFilter();
        $starterClass = $this->normalizeStarterClass($this->argument('modelStarterClass'));
        $this->isForce = $this->option('force');

        if (!$starterClass) {
            $this->error("Please use scaffold:model Class\To\ModelStarter");
        } elseif (!class_exists($starterClass)) {
            $this->error($starterClass . " doesn't exist");
        } elseif (!$this->isModelStarter($starterClass)) {
            $this->error($starterClass . " is not a ModelStarter");
        } else {
            $this->igniteStarter($starterClass);
        }
    }

    private function normalizeGeneratorFilter()
    {
        $this->onlyGenerators = $this->option('only');
        foreach ($this->onlyGenerators as $key => $only) {
            $this->onlyGenerators[$key] = snake_case($only);
        }
    }

    private function normalizeStarterClass($raw)
    {
        $starterClass = str_replace('/', '\\', $raw);
        if (ends_with($starterClass, '.php')) {
            $starterClass = substr($starterClass, 0, -4);
        }

        return $starterClass;
    }

    private function isModelStarter($className)
    {
        $ref = new \ReflectionClass($className);
        return $ref->isSubclassOf(ModelStarter::class);
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
        $this->moduleDirectoryName = config('scaffold.modules', 'Modules');
        $this->starter = new $starterClass();
        $this->namespace = $this->starter->getModuleNameSpace();
        $this->folder = $this->starter->getModuleFolder();

        $this->info('Generating ' . $this->namespace . ' to ' . $this->folder . '...');

        $this->model = $this->starter->getModel();
        $this->generateModel();
        $this->generateRepository();
        $this->generateRequest();
        $this->generateController();
        $this->generateMigration();
        $this->generateLanguageFiles();
        $this->generateRoutes();

        $this->generateFactory();
        $this->generatePHPUnitTests();

        $this->generateFrontEndAPIs();

        $this->generateFrontEndPages();
        $this->generateFrontEndRoutes();

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
    private function getDestinationPath($configPath, $modelName = null, $frontend = false)
    {
        $path = $frontend ? '' : $this->folder . DIRECTORY_SEPARATOR;
        $path = $path . config('scaffold.templates.' . $configPath)[1];
        $path = Utils::fillTemplate([
            'MODEL_NAME'  => $modelName,
            'MODULE_NAME' => $this->starter->getModuleName()
        ], $path);
        return $path;
    }

    private function generateController()
    {
        $this->comment("Controller...");
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
            config('scaffold.templates.controller', 'scaffold::raw.Controller'),
            $variables);

        $destinationPath = $this->getDestinationPath('controller', $pascalCase);
        Utils::writeFile($destinationPath, $content);

        /**
         * Permissions are generated with controller actions
         * @var PermissionRepository $permissionRepository
         **/
        $permissionRepository = app(PermissionRepository::class);
        $permissionRepository->firstOrCreate(['name' => "{$pascalCase}@destroy", 'guard_name' => 'api', 'label' => '']);
        $permissionRepository->firstOrCreate(['name' => "{$pascalCase}@index", 'guard_name' => 'api', 'label' => '']);
        $permissionRepository->firstOrCreate(['name' => "{$pascalCase}@show", 'guard_name' => 'api', 'label' => '']);
        $permissionRepository->firstOrCreate(['name' => "{$pascalCase}@store", 'guard_name' => 'api', 'label' => '']);
        $permissionRepository->firstOrCreate(['name' => "{$pascalCase}@update", 'guard_name' => 'api', 'label' => '']);

        return;
    }

    private function generateRequest()
    {
        $this->comment("Request...");
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
            config('scaffold.templates.request.create', 'scaffold::raw.Requests.Create'),
            $variables);
        $updateContent = Utils::renderTemplate(
            config('scaffold.templates.request.update', 'scaffold::raw.Requests.Update'),
            $variables);

        $createDestinationPath = $this->getDestinationPath('request.create', $pascalCase);
        $updateDestinationPath = $this->getDestinationPath('request.update', $pascalCase);
        Utils::writeFile($createDestinationPath, $createContent);
        Utils::writeFile($updateDestinationPath, $updateContent);

        return;
    }

    private function generateRepository()
    {
        $this->comment("Repository...");
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
            config('scaffold.templates.repository', 'scaffold::raw.Repository'),
            $variables);

        $destinationPath = $this->getDestinationPath('repository', $pascalCase);
        Utils::writeFile($destinationPath, $content);

        return;
    }

    private function generateModel()
    {
        $this->comment("Model...");
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
            config('scaffold.templates.model', 'scaffold::raw.Model'),
            $variables);
        $destinationPath = $this->getDestinationPath('model', $pascalCase);
        Utils::writeFile($destinationPath, $content);

        return;
    }


    private function generateMigration()
    {
        $this->comment("Migration File...");
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

    private function generateFrontEndAPIs()
    {
        $this->comment("Front End APIs...");
        if (!$this->checkWilling('api')) {
            return $this->line("\tskip");
        }

        $modelName = camel_case($this->starter->getModelName());
        $route = $this->model->getRoute();
        Utils::createFrontEndAPI("{$modelName}Index", 'get', "{$route}", $modelName);
        Utils::createFrontEndAPI("{$modelName}Store", 'post', "{$route}", $modelName);
        Utils::createFrontEndAPI("{$modelName}Show", 'get', "{$route}/{id}", $modelName);
        Utils::createFrontEndAPI("{$modelName}Update", 'put', "{$route}/{id}", $modelName);
        Utils::createFrontEndAPI("{$modelName}Destroy", 'delete', "{$route}/{id}", $modelName);

        $this->info('Front End APIs are updated.');
        return;
    }

    /**
     * 生成 front end pages
     * resources
     */
    private function generateFrontEndPages()
    {
        $this->comment("Front End Pages...");
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

        $listPageContent = Utils::renderTemplate(
            config('scaffold.templates.frontend.pages.list', 'scaffold::frontend.List'),
            $variables);
        $editorPageContent = Utils::renderTemplate(
            config('scaffold.templates.frontend.pages.editor', 'scaffold::frontend.Editor'),
            $variables);
        $listPagePath = $this->getDestinationPath('frontend.pages.list', $pascalCase, true);
        $editorPagePath = $this->getDestinationPath('frontend.pages.editor', $pascalCase, true);
        Utils::writeFile($listPagePath, $listPageContent);
        Utils::writeFile($editorPagePath, $editorPageContent);
    }

    /**
     * 生成 routes and menu
     */
    private function generateFrontEndRoutes()
    {
        $this->comment("Resources routes...");
        if (!$this->checkWilling('resource')) {
            return $this->line("\tskip");
        }

        $variables = [
            'STARTER' => $this->starter,
            'MODEL'   => $this->starter->getModel(),
        ];
        $routeLine = Utils::renderTemplate(
            config('scaffold.templates.frontend.routes', 'scaffold::frontend.routes'),
            $variables);
        $routesPath = config('scaffold.templates.frontend.routes')[1];
        Utils::replaceFilePlaceholders($routesPath, [
            "\n  // Append More Routes. Don't remove me" => ",\n" .
                '  ' . $routeLine . "\n  // Append More Routes. Don't remove me"
        ], null, '');
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
        $enContent = Utils::renderTemplate('scaffold::raw.lang.en.t', $variables);
        $zhCnContent = Utils::renderTemplate('scaffold::raw.lang.zh-CN.t', $variables);

        $model_snake_case = snake_case($this->starter->getModelName());
        $folder = resource_path('lang/');
        Utils::writeFile($folder . "/en/{$model_snake_case}.php", $enContent);
        Utils::writeFile($folder . "/zh-CN/{$model_snake_case}.php", $zhCnContent);

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
        $routeLine = Utils::renderTemplate(
            config('scaffold.templates.routes', 'scaffold::raw.routes'),
            $variables);

        $destinationPath = $this->folder . DIRECTORY_SEPARATOR . "routes.php";
        $routeFile = file_get_contents($destinationPath);
        if (str_contains($routeFile, $routeLine)) {
            $this->info('Route config exists. Skip.');
            return;
        }

        Utils::replaceFilePlaceholders($destinationPath, [
            '<?php' => "<?php\n" . $routeLine], null, '');

        $this->info('Route config is updated.');
        return;
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

    private function generateFactory()
    {
        $this->comment("Database Factory...");
        if (!$this->checkWilling('factory')) {
            return $this->line("\tskip");
        }

        $variables = [
            'MODEL'           => $this->model,
            'moduleNamespace' => $this->starter->getModuleNameSpace()
        ];
        $modelName = $this->model->getModelName();
        $routesPath = database_path("factories/{$modelName}Factory.php");
        $routesContent = Utils::renderTemplate(
            config('scaffold.templates.factory', 'scaffold::factory'),
            $variables);
        Utils::writeFile($routesPath, $routesContent);

        $this->info('factory is updated.');
        return;
    }

    private function generatePHPUnitTests()
    {
        $this->comment("PHPUnit Tests...");
        if (!$this->checkWilling('phpunit')) {
            return $this->line("\tskip");
        }

        $modelName = $this->starter->getModelName();
        $route = $this->model->getRoute();
        $controller = $this->starter->getModelName();
        $moduleName = $this->starter->getModuleName();
        Utils::createPHPUnit($controller, "{$modelName}Index", 'get', "{$route}", $moduleName);
        Utils::createPHPUnit($controller, "{$modelName}Store", 'post', "{$route}", $moduleName);
        Utils::createPHPUnit($controller, "{$modelName}Show", 'get', "{$route}/{id}", $moduleName);
        Utils::createPHPUnit($controller, "{$modelName}Update", 'put', "{$route}/{id}", $moduleName);
        Utils::createPHPUnit($controller, "{$modelName}Destroy", 'delete', "{$route}/{id}", $moduleName);

        $this->info('PHPUnit Tests are updated.');
        return;
    }
}