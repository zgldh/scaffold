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
        $path = __DIR__ . '/../../templates/' . $name;
        return $path;
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
        $template = file_get_contents($filePath);
        $templateData = Utils::fillTemplate($variables, $template, $pending);
        if (!$destPath) {
            $destPath = $filePath;
        }
        file_put_contents($destPath, $templateData);
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
        Utils::replaceFilePlaceholders($adminEntryPath, [
            '// Modules routes' => '  ' . $adminVueRoutesPath . ",\n" . '// Modules routes'
        ], null, '');
    }
}