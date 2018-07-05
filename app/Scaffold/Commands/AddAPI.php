<?php namespace App\Scaffold\Commands;

use App\Scaffold\Installer\Utils;
use Illuminate\Console\Command;
use Modules\User\Repositories\PermissionRepository;

class AddAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:api {method} {route} {moduleName} {--controller=} {--action=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API to a module.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $moduleName = null;
    private $controllerName = null;
    private $actionName = null;
    private $routeParameters = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $method = strtolower($this->argument('method'));
        $route = trim(strtolower($this->argument('route')), '/');
        $this->moduleName = ucfirst(camel_case($this->argument('moduleName')));

        $routesFilePath = $this->checkAndGetRoutesFile();
        if (!$routesFilePath) {
            $this->error("Module {$this->moduleName} doesn't have routes file.");
            $this->error("{$routesFilePath} must be exists.");
            return false;
        }

        $this->controllerName = $this->getControllerName($route);
        $this->actionName = $this->getActionName($method, $route);
        $this->routeParameters = $this->getRouteParameters($route);
        $routeLine = $this->generateRouteLine($method, $route);
        $this->saveToRoute($routeLine, $routesFilePath);
        $this->line($routesFilePath);
//
        $requestPath = $this->createRequestClass();
        $this->line($requestPath);

        $controllerPath = $this->createControllerAction($method, $route);
        $this->line($controllerPath);

        $frontEndAPI = $this->createFrontEndAPI($method, $route);
        $this->line($frontEndAPI);

        $phpUnitPath = $this->createPHPUnit($method, $route);
        $this->line($phpUnitPath);

        $permissionName = $this->createPermission($method, $route);
        $this->line($permissionName);

        $this->info('Complete');
    }

    /**
     * @param $this ->moduleName
     * @return bool|string
     */
    private function checkAndGetRoutesFile()
    {
        $routesFilePath = $this->getModulePath('routes.php');
        if (!file_exists($routesFilePath)) {
            return false;
        }
        return $routesFilePath;
    }

    private function generateRouteLine($method, $route)
    {
        $method = strtolower($method);
        $route = strtolower($route);
        $line = view('scaffold::routeLine', [
            'method'         => $method,
            'route'          => $route,
            'moduleName'     => $this->moduleName,
            'controllerName' => $this->controllerName,
            'actionName'     => $this->actionName
        ])->render();
        return $line;
    }

    private function getControllerName($route)
    {
        $inputControllerName = ucfirst(camel_case($this->option('controller')));
        if ($inputControllerName) {
            if (ends_with($inputControllerName, 'Controller')) {
                $controllerName = $inputControllerName;
            } else {
                $controllerName = $inputControllerName . 'Controller';
            }
        } else {
            $parts = preg_split('/\//', $route);
            $controllerName = ucfirst(camel_case($parts[0])) . 'Controller';
        }
        return $controllerName;
    }

    private function getRouteParameters($route)
    {
        $parameters = [];
        preg_match_all('/\{(.*?)\}/', $route, $parameters);
        $parameters = $parameters[1];
        return $parameters;
    }

    private function getActionName($method, $route)
    {
        $inputActionName = camel_case($this->option('action'));
        if ($inputActionName) {
            $actionName = $inputActionName;
        } else {
            $parts = preg_split('/\//', $route);
            array_shift($parts);
            $controllerName = preg_replace('/\{|\}/', ' ', join(' ', $parts));
            $controllerName = ucfirst(camel_case($controllerName));
            $actionName = $method . $controllerName;
        }
        return $actionName;
    }

    private function saveToRoute($routeLine, $routesFilePath)
    {
        $routeFile = file_get_contents($routesFilePath);
        if (str_contains($routeFile, $routeLine)) {
            $this->info('Route config exists. Skip.');
            return;
        }

        Utils::replaceFilePlaceholders($routesFilePath, [
            '<?php' => "<?php\n" . $routeLine], null, '');

        $this->info('Route config is updated.');
    }

    /**
     * Create Request Class
     * @return string
     */
    private function createRequestClass()
    {
        $requestClassName = $this->getRequestClass();
        $requestFilePath = $this->getModulePath("Requests/{$requestClassName}.php");

        if (file_exists($requestFilePath)) {
            $this->info('Request file exists. Skip.');
            return $requestFilePath;
        }

        $requestContent = view('scaffold::request', [
            'moduleNameSpace' => $this->getModuleNamespace(),
            'className'       => $requestClassName,
        ])->render();

        Utils::writeFile($requestFilePath, $requestContent);
        $this->info('Request class is set: ' . $requestClassName);
        $this->line($requestFilePath);

        return $requestFilePath;
    }

    private function createControllerAction($method, $route)
    {
        $controllerClassName = $this->controllerName;
        $controllerFilePath = $this->getModulePath("Controllers/{$controllerClassName}.php");

        if (!file_exists($controllerFilePath)) {
            $this->info("Controller doesn't exists. Generating.");
            $this->generateSimpleController($controllerClassName, $controllerFilePath);
        }

        $requestFullClass = $this->getRequestClass(true);
        $this->addAction($controllerFilePath, $route, $this->actionName, $requestFullClass);

        return $controllerFilePath;
    }

    private function generateSimpleController($controllerClassName, $controllerPath)
    {
        $requestContent = view('scaffold::controller-simple', [
            'moduleNameSpace' => $this->getModuleNamespace(),
            'className'       => $controllerClassName,
        ])->render();

        Utils::writeFile($controllerPath, $requestContent);
    }

    private function addAction($controllerPath, $route, $actionName, $requestFullClass)
    {
        $controllerContent = file_get_contents($controllerPath);

        if (str_contains($controllerContent, "function {$actionName}(")) {
            $this->info("Action {$actionName} exists. Skip.");
            return;
        }

        $parameters = $this->routeParameters;
        $parameters = array_map(function ($item) {
            return '$' . $item;
        }, $parameters);
        array_push($parameters, '\\' . $requestFullClass . ' $request');

        $actionContent = view('scaffold::action', [
            'actionName' => $actionName,
            'parameters' => $parameters,
        ])->render();

        $lastBraceIndex = strrpos($controllerContent, '}');
        $controllerContent = substr($controllerContent, 0, $lastBraceIndex) . "\n" . $actionContent . "\n}\n";

        Utils::writeFile($controllerPath, $controllerContent);
        $this->info("Action {$actionName} is set");
    }

    private function createFrontEndAPI($method, $route)
    {
        $apiPath = base_path(config('scaffold.frontend_folder', 'frontend')
            . '/src/api/' . strtolower($this->moduleName) . '.js');
        if (!file_exists($apiPath)) {
            $this->info("API file doesn't exists. Generating.");
            Utils::writeFile($apiPath, view('scaffold::frontend.api.file')->render());
        }

        $apiFunctionName = $this->actionName;
        $apiFileContent = file_get_contents($apiPath);
        if (str_contains($apiFileContent, "export function {$apiFunctionName}(")) {
            $this->info('Vue API exists. Skip.');
            return $apiPath;
        }

        if (!ends_with($apiFileContent, "\n")) {
            $apiFileContent .= "\n";
        }

        $parameters = $this->routeParameters;
        if ($method == 'post' || $method == 'put') {
            $parameters[] = 'data';
        }
        $route = '/' . trim($route, '/\\');
        $route = str_replace('{', '${', $route);
        $apiFileContent .= "\n" . view('scaffold::frontend.api.' . $method, [
                'functionName' => $apiFunctionName,
                'route'        => $route,
                'parameters'   => $parameters
            ])->render() . "\n";
        Utils::writeFile($apiPath, $apiFileContent);

        $this->info("Front end API {$apiFunctionName} is set");
        return $apiPath;
    }

    private function createPHPUnit($method, $route)
    {
        // Check tests/Modules/<ModuleName>/<ControllerName> directory
        $controllerName = $this->getControllerPureName();

        // Check API test class
        $testClassName = ucfirst($this->actionName) . 'Test';
        $testFilePath = base_path("tests/Modules/{$this->moduleName}/{$controllerName}/{$testClassName}.php");
        if (file_exists($testFilePath) === true) {
            $this->info("PHPUnit Class exists. Skip.");
            return $testFilePath;
        }

        $testFileContent = view('scaffold::phpunit.basic', [
            'moduleNameSpace'  => "{$this->moduleName}\\{$controllerName}",
            'testClassName'    => $testClassName,
            'testFunctionName' => ucfirst($this->actionName),
            'method'           => $method,
            'route'            => 'api/' . $route
        ])->render();

        Utils::writeFile($testFilePath, $testFileContent);

        $this->info("PHPUnit {$testClassName} is set");
        return $testFilePath;
    }

    private function createPermission($method, $route)
    {
        $controllerPureName = $this->getControllerPureName();
        $actionName = $this->actionName;
        $permissionName = "{$controllerPureName}@{$actionName}";
        /**
         * Permissions are generated with controller actions
         * @var PermissionRepository $permissionRepository
         **/
        $permissionRepository = app(PermissionRepository::class);
        $permissionRepository->firstOrCreate(['name' => $permissionName, 'guard_name' => 'api', 'label' => '']);

        $this->info("Permission is set");
        return $permissionName;
    }

    /**
     * PostController will turns to Post
     * @return bool|string
     */
    private function getControllerPureName()
    {
        return substr($this->controllerName, 0, -10);
    }

    private function getRequestClass($full = false)
    {
        $className = ucfirst($this->actionName) . "Request";
        if ($full) {
            $className = $this->getModuleNamespace("Requests\\" . $className);
        }
        return $className;
    }

    private function getModulePath($relativeFilePath = null)
    {
        $relativeFilePath = str_replace('\\', '/', trim($relativeFilePath, " \t\n\r\0\x0B/\\"));

        $path = base_path("Modules/{$this->moduleName}");
        if ($relativeFilePath) {
            $path = $path . '/' . $relativeFilePath;
        }
        return $path;
    }

    private function getModuleNamespace($relativeNamespace = null)
    {
        $relativeNamespace = str_replace('/', '\\', trim($relativeNamespace, " \t\n\r\0\x0B/\\"));

        $namespace = "Modules\\{$this->moduleName}";
        if ($relativeNamespace) {
            $namespace = $namespace . "\\" . $relativeNamespace;
        }
        return $namespace;
    }
}
