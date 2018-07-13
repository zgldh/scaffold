<?php namespace App\Scaffold\Commands;

use App\Scaffold\Installer\Utils;
use Illuminate\Console\Command;

class AddModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:module {moduleName} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module.';

    private $isForce = false;
    private $moduleName = null;
    private $moduleDirectory = null;
    private $frontendStoreDirectory = null;

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
        $this->moduleName = ucfirst(camel_case($this->argument('moduleName')));
        $this->isForce = $this->option('force');

        $this->moduleDirectory = $this->makeModuleDirectory($this->moduleName);
        $this->frontendStoreDirectory = base_path("frontend/src/store/modules");

        $routePath = $this->createRouteFile();
        $serviceProviderPath = $this->createServiceProvider();
        $this->createFrontEndStore();

        $this->appendRoute();
        $this->appendServiceProvider();

        $this->line($routePath);
        $this->line($serviceProviderPath);

        $this->info('Complete');
    }

    /**
     * @param $moduleName
     * @return string
     */
    private function makeModuleDirectory($moduleName)
    {
        $moduleDirectory = base_path("Modules/{$moduleName}");
        if (!is_dir($moduleDirectory)) {
            mkdir($moduleDirectory);
        }
        @mkdir($moduleDirectory . '/Controllers');
        @mkdir($moduleDirectory . '/Models');
        @mkdir($moduleDirectory . '/Repositories');
        @mkdir($moduleDirectory . '/Requests');
        @mkdir($moduleDirectory . '/resources/views', 0777, true);
        file_put_contents($moduleDirectory . '/resources/views/.gitkeep', '');
        return $moduleDirectory;
    }

    /**
     * Create routes.php
     * @return string
     */
    private function createRouteFile()
    {
        $routeFileName = "routes.php";
        $routeFilePath = $this->moduleDirectory . DIRECTORY_SEPARATOR . $routeFileName;

        if (file_exists($routeFilePath) && $this->isForce == false) {
            $this->info('Route file exists. Skip.');
            return $routeFilePath;
        }

        $routeFileContent = "<?php\n\n";
        file_put_contents($routeFilePath, $routeFileContent);
        $this->info('Route file is set: ' . $routeFileName);
        $this->line($routeFilePath);

        return $routeFilePath;
    }

    /**
     * Create Service Provider class
     * @return string
     */
    private function createServiceProvider()
    {
        $serviceProviderClass = $this->moduleName . "ServiceProvider";
        $serviceProviderFileName = $serviceProviderClass . '.php';
        $serviceProviderPath = $this->moduleDirectory . DIRECTORY_SEPARATOR . $serviceProviderFileName;

        if (file_exists($serviceProviderPath) && $this->isForce == false) {
            $this->info($serviceProviderFileName . ' file exists. Skip.');
            return $serviceProviderPath;
        }

        $serviceProviderContent = $this->generateServiceProviderContent();
        file_put_contents($serviceProviderPath, $serviceProviderContent);
        $this->info('ServiceProvider file is set: ' . $serviceProviderFileName);
        $this->line($serviceProviderPath);

        return $serviceProviderPath;
    }

    /**
     * Create FrontEnd Store file
     * @return string
     */
    private function createFrontEndStore()
    {
        $moduleName = lcfirst($this->moduleName);
        $storeFileName = $moduleName . '.js';
        $storeFilePath = $this->frontendStoreDirectory . DIRECTORY_SEPARATOR . $storeFileName;

        if (file_exists($storeFilePath) && $this->isForce == false) {
            $this->info($storeFileName . ' file exists. Skip.');
            return $storeFilePath;
        }

        $storeContent = $this->generateStoreContent($moduleName);
        file_put_contents($storeFilePath, $storeContent);
        $this->appendStore($moduleName);
        $this->info('Store file is set: ' . $storeFileName);
        $this->line($storeFilePath);

        return $storeFilePath;
    }

    private function generateStoreContent($moduleName)
    {
        $line = view('scaffold::frontend.store', [
            'moduleNameSpace' => "Modules\\" . ucfirst($moduleName),
            'moduleName'      => $moduleName,
        ])->render();
        return $line;
    }

    private function generateServiceProviderContent()
    {
        $line = view('scaffold::serviceProvider', [
            'moduleNameSpace' => "Modules\\" . $this->moduleName,
            'moduleName'      => $this->moduleName,
        ])->render();
        return $line;
    }

    private function appendStore($moduleName)
    {
        $storeIndexFile = base_path('frontend/src/store/index.js');
        $content = file_get_contents($storeIndexFile);
        $content = "import {$moduleName} from './modules/{$moduleName}'\n" . $content;
        $key = '// Append More Stores. Don\'t remove me';
        $content = Utils::fillTemplate([
            $key => $key . "\n    {$moduleName},"], $content, '');

        file_put_contents($storeIndexFile, $content);
    }

    private function appendRoute()
    {
        $routeLine = 'require(base_path(\'Modules/' . $this->moduleName . '/routes.php\'));';
        Utils::addRoute($routeLine, 'api', '});');
    }

    private function appendServiceProvider()
    {
        $serviceProviderClass = '\Modules\\' . $this->moduleName . '\\' . $this->moduleName . 'ServiceProvider::class';
        Utils::addServiceProvider($serviceProviderClass);
    }
}
