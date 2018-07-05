<?php namespace App\Scaffold\Installer;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2016/11/20
 * Time: 23:49
 */

class Utils
{
    public static function template($name)
    {
        $path = realpath(__DIR__ . '/../../templates/' . $name);
        return $path;
    }

    public static function renderTemplate($name, $data)
    {
        if (is_array($name)) {
            $name = $name[0];
        }
        if (ends_with($name, '.stub')) {
            $result = self::fillTemplate($data, file_get_contents(self::template($name)));
        } else {
            $result = view($name, $data)->render();
        }
        return $result;
    }

    public static function writeFile($name, $content)
    {
        $folder = dirname($name);
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        file_put_contents($name, $content);
    }

    public static function fillTemplate($variables, $template, $pending = '$')
    {
        foreach ($variables as $variable => $value) {
            $template = str_replace($pending . $variable . $pending, $value, $template);
        }

        return $template;
    }

    public static function copy($src, $dst, $variables = null)
    {
        if (is_dir($src)) {
            if (!file_exists($dst)) {
                mkdir($dst, 0755, true);
            }
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    self::copy("$src" . DIRECTORY_SEPARATOR . "$file", "$dst" . DIRECTORY_SEPARATOR . "$file",
                        $variables);
                }
            }
        } elseif (file_exists($src)) {
            if ($variables) {
                self::replaceFilePlaceholders($src, $variables, $dst);
            } else {
                copy($src, $dst);
            }
        }
    }

    /**
     * Replace and Save
     * @param $filePath
     * @param $variables
     * @param null $destPath
     */
    public static function replaceFilePlaceholders($filePath, $variables, $destPath = null, $pending = '$')
    {
        if (!$destPath) {
            $destPath = $filePath;
        }
        if (is_dir($filePath)) {
            $files = scandir($filePath);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    Utils::replaceFilePlaceholders("$filePath" . DIRECTORY_SEPARATOR . "$file", $variables, $destPath,
                        $pending);
                }
            }
        } elseif (file_exists($filePath)) {
            $template = file_get_contents($filePath);
            $templateData = Utils::fillTemplate($variables, $template, $pending);
            $folder = dirname($destPath);
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            file_put_contents($destPath, $templateData);
        }

    }

    /**
     * Update locale from config/app.php
     * @param $locale
     */
    public static function setAppLocale($locale)
    {
        $appConfigPath = base_path('config/app.php');
        $appConfig = file_get_contents($appConfigPath);
        $appConfig = preg_replace('/(.*\'locale\' => \')([a-zA-Z\-]*)(\'.*)/', '$1' . $locale . '$3', $appConfig);
        file_put_contents($appConfigPath, $appConfig);
    }

    /**
     * Add service provider settings into config/app.php
     * @param $serviceProviderClassName
     */
    public static function addServiceProvider($serviceProviderClassName)
    {
        $appConfigPath = base_path('config/app.php');
        $appConfig = file_get_contents($appConfigPath);
        if (!str_contains($appConfig, $serviceProviderClassName)) {
            $providerEndIndex = strpos($appConfig, "]", strpos($appConfig, " 'providers' => ["));
            $appConfig = substr($appConfig, 0, $providerEndIndex) .
                "    " . $serviceProviderClassName . ",\n    " .
                substr($appConfig, $providerEndIndex);
            file_put_contents($appConfigPath, $appConfig);
        }
    }

    /**
     * Add service alias settings into config/app.php
     * @param $alias
     * @param $facade
     */
    public static function addAlias($alias, $facade)
    {
        $appConfigPath = base_path('config/app.php');
        $appConfig = file_get_contents($appConfigPath);
        if (!str_contains($appConfig, $facade)) {
            $aliasContent = "'{$alias}'     => {$facade}";
            $providerEndIndex = strpos($appConfig, "]", strpos($appConfig, " 'aliases' => ["));
            $appConfig = substr($appConfig, 0, $providerEndIndex) .
                "    " . $aliasContent . ",\n    " .
                substr($appConfig, $providerEndIndex);
            file_put_contents($appConfigPath, $appConfig);
        }
    }

    /**
     * Add route setting to route files under /routes/*.php
     * @param $route
     */
    public static function addRoute($route, $file = 'web', $before = '\n')
    {
        $route = str_replace('\\', '/', $route);
        $routeFilePath = base_path('routes/' . $file . '.php');
        $routeFile = file_get_contents($routeFilePath);
        if (!str_contains($routeFile, $route)) {
            $routeEndIndex = strrpos($routeFile, $before);
            $routeFile = substr($routeFile, 0, $routeEndIndex) .
                "    " . $route . "\n" .
                substr($routeFile, $routeEndIndex);
            file_put_contents($routeFilePath, $routeFile);
        }
    }

    /**
     * Add vue route setting to admin JavaScript entry file.
     * @param $adminVueRoutesPath
     * @param string $adminJs
     */
    public static function addToVueRoute($adminVueRoutesPath, $adminJs = 'assets/js/entries/admin.js')
    {
        $adminEntryPath = resource_path($adminJs);
        $adminEntryFile = file_get_contents($adminEntryPath);
        if (!str_contains($adminEntryFile, $adminVueRoutesPath)) {
            Utils::replaceFilePlaceholders($adminEntryPath, [
                '// Modules routes' => '  ' . $adminVueRoutesPath . ",\n" . '// Modules routes'
            ], null, '');
        }
    }

    /**
     * Add menu item to admin blade template file.
     * @param $menuContent
     */
    public static function addAdminMenuItem($menuContent)
    {
        $menuPath = resource_path('views/admin/menu.blade.php');
        $menus = file_get_contents($menuPath);
        if (!str_contains($menus, $menuContent)) {
            file_put_contents($menuPath, $menus . "\n" . $menuContent . "\n");
        }
    }

    /**
     * 输出好看的数组
     * @param $val
     * @return mixed|string
     */
    public static function exportArray($val)
    {
        if (isset($val[0])) {
            $array = json_encode($val, JSON_PRETTY_PRINT);
        } else {
            $array = var_export($val, true);
            $array = trim($array, ' array()');
            $array = '[' . $array . ']';
            $array = str_replace(["array (\n", "),\n"], ["[\n", "],\n"], $array);
        }
        return $array;
    }

    /**
     * 将数组转换成字符串
     * @param $arr
     * @param string $separator 分隔符
     * @param string $comma     引号包裹
     * @return mixed
     */
    public static function arrayToString($arr, $separator = ',', $comma = '\'')
    {
        $result = array_reduce($arr, function ($carry, $param) use ($separator, $comma) {
            $segment = $comma . $param . $comma;
            return $carry ? $carry . "{$separator} {$segment}" : "{$segment}";
        }, null);
        return $result;
    }

    /**
     * 数据库迁移文件是否存在
     * @param $className
     * @return bool
     */
    public static function isMigrationFileExists($className)
    {
        $fileTail = snake_case($className) . '.php';
        $files = glob(database_path('migrations/*'));
        foreach ($files as $file) {
            if ($fileTail === substr(basename($file), 18)) {
                return $file;
            }
        }
        return false;
    }

    public static function generateTargetModelRoute($modelName)
    {
        $modulesNamespace = config('scaffold.modules');
        $parts = preg_split('/\\\\/', $modelName);
        $parts = array_values(array_diff($parts, [$modulesNamespace, 'Models']));
        $module = $parts[0];
        $model = $parts[1];
        if ($module !== $model) {
            return snake_case($module) . '/' . snake_case($model);
        }
        return snake_case($model);
    }

    public static function getTargetModelSearchColumns($modelName)
    {
        if (class_exists($modelName) === false) {
            return ['id'];
        }
        $model = new $modelName;
        $table = $model->table;
        $tester = ['title', 'name', 'subject', 'label', 'value', 'first_name', 'head_line', 'content', 'id'];
        $column = 'id';
        foreach ($tester as $testColumn) {
            if (\Schema::hasColumn($table, $testColumn)) {
                $column = $testColumn;
                break;
            }
        }
        return [$column];
    }

    /**
     * @param $controllerName
     * @param $actionName
     * @param $method
     * @param $route
     * @param $moduleName
     * @return string
     */
    public static function createPHPUnit($controllerName, $actionName, $method, $route, $moduleName)
    {
        // Check tests/Modules/<ModuleName>/<ControllerName> directory
        if (ends_with($controllerName, 'Controller')) {
            $controllerName = substr($controllerName, 0, -10);
        }

        // Check API test class
        $testClassName = ucfirst($actionName) . 'Test';
        $testFilePath = str_replace('\\', '/', base_path("tests/Modules/{$moduleName}/{$controllerName}/{$testClassName}.php"));
        if (file_exists($testFilePath) === true) {
            return $testFilePath;
        }

        $testFileContent = view('scaffold::phpunit.basic', [
            'moduleNameSpace'  => "{$moduleName}\\{$controllerName}",
            'testClassName'    => $testClassName,
            'testFunctionName' => ucfirst($actionName),
            'method'           => $method,
            'route'            => 'api/' . $route
        ])->render();

        Utils::writeFile($testFilePath, $testFileContent);

        return $testFilePath;
    }

    /**
     * @param $actionName
     * @param $method
     * @param $route
     * @param $modelName
     * @return string
     */
    public static function createFrontEndAPI($actionName, $method, $route, $modelName)
    {
        $apiPath = base_path(config('scaffold.frontend_folder', 'frontend')
            . '/src/api/' . $modelName . '.js');
        if (!file_exists($apiPath)) {
            Utils::writeFile($apiPath, view('scaffold::frontend.api.file')->render());
        }

        $apiFunctionName = $actionName;
        $apiFileContent = file_get_contents($apiPath);
        if (str_contains($apiFileContent, "export function {$apiFunctionName}(")) {
            return $apiPath;
        }

        if (!ends_with($apiFileContent, "\n")) {
            $apiFileContent .= "\n";
        }

        $route = '/' . trim($route, '/\\');

        $parameters = [];
        preg_match_all('/\{(.*?)\}/', $route, $parameters);
        $parameters = $parameters[1];
        if ($method == 'post' || $method == 'put') {
            $parameters[] = 'data';
        }

        $route = str_replace('{', '${', $route);
        $apiFileContent .= view('scaffold::frontend.api.' . $method, [
                'functionName' => $apiFunctionName,
                'route'        => $route,
                'parameters'   => $parameters
            ])->render() . "\n";
        Utils::writeFile($apiPath, $apiFileContent);

        return $apiPath;
    }
}