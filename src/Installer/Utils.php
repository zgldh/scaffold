<?php namespace zgldh\Scaffold\Installer;

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
        if (ends_with($name, '.stub')) {
            $result = self::fillTemplate($data, file_get_contents(self::template($name)));
        } else {
            $result = view('zgldh.scaffold::' . $name, $data)->render();
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
    public static function addRoute($route, $file = 'web')
    {
        $routeFilePath = base_path('routes/' . $file . '.php');
        $routeFile = file_get_contents($routeFilePath);
        if (!str_contains($routeFile, $route)) {
            $routeFile .= "\n" . $route;
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
        }
        return $array;
    }
}