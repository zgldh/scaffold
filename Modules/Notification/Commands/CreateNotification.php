<?php namespace Modules\Notification\Commands;

use App\Scaffold\Installer\Utils;
use Illuminate\Console\Command;

class CreateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create {moduleName} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a notification for zgldh/scaffold';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Move template to module folder
        $name = studly_case($this->argument('name'));
        $module = studly_case($this->argument('moduleName'));
        $mailTemplateName = camel_case($name);
        $namespace = $this->getNamespace($module);
        $classPath = $this->getPath($module, $name);
        $viewPath = $this->getViewPath($module, $mailTemplateName);

        $this->saveClass($module, $name, $namespace, $classPath, $mailTemplateName);
        $this->saveMailTemplate($viewPath);
        $this->saveLanguage($namespace, $name);

        $this->line($classPath);
        $this->line($viewPath);

        $this->info('Complete');
    }

    private function getNamespace($module)
    {
        return 'Modules\\' . $module . '\\Notifications';
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($module, $name)
    {
        $path = base_path(config('scaffold.modules', 'Modules') . '/'
            . $module . '/Notifications/' . $name . '.php');
        return $path;
    }

    private function getViewPath($module, $mailTemplateName)
    {
        $path = base_path(config('scaffold.modules', 'Modules') . '/'
            . $module . '/resources/views/' . $mailTemplateName . '.blade.php');
        return $path;
    }

    private function saveClass($module, $name, $namespace, $classPath, $mailTemplateName)
    {
        if (!is_dir(dirname($classPath))) {
            mkdir(dirname($classPath), 0755, true);
        }
        $classContent = file_get_contents(__DIR__ . '/stubs/notification.stub');
        $classContent = str_replace('DummyNamespace', $namespace, $classContent);
        $classContent = str_replace('DummyClass', $name, $classContent);
        $classContent = str_replace('DummyView', 'Modules\\' . $module . '::' . $mailTemplateName, $classContent);

        file_put_contents($classPath, $classContent);
    }

    private function saveMailTemplate($viewPath)
    {
        if (!is_dir(dirname($viewPath))) {
            mkdir(dirname($viewPath), 0755, true);
        }
        $viewContent = file_get_contents(base_path('vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/markdown.stub'));
        file_put_contents($viewPath, $viewContent);
    }

    private function saveLanguage($namespace, $name)
    {
        $typeKey = $namespace . '\\' . $name;

        $folder = resource_path("lang/*/notification.php");
        foreach (glob($folder) as $langFile) {
            $langFileContent = require $langFile;
            if (!isset($langFileContent['types'])) {
                $langFileContent['types'] = [];
            }
            if (!isset($langFileContent['types'][$typeKey])) {
                $langFileContent['types'][$typeKey] = $name;
                $langContent = "<?php\n\nreturn " . Utils::exportArray($langFileContent) . ";\n";
                file_put_contents($langFile, $langContent);
            }
        }
    }
}
