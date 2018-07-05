<?php namespace App\Scaffold\Installer;

class KernelEditor
{
    public static function read()
    {
        $kernelPath = app_path('Http/Kernel.php');
        return file_get_contents($kernelPath);
    }

    public static function save($content)
    {
        $kernelPath = app_path('Http/Kernel.php');
        file_put_contents($kernelPath, $content);
    }

    private static function insertBetween($content, $first, $end, $insertContent)
    {
        $middlewareEndIndex = strpos($content, $end, strpos($content, $first));
        $content = substr($content, 0, $middlewareEndIndex) .
            "    " . $insertContent . ",\n    " .
            substr($content, $middlewareEndIndex);
        return $content;
    }

    public static function addMiddlewareToWebGroup($middleware)
    {
        $kernelFile = self::read();
        if (!str_contains($kernelFile, $middleware)) {
            $kernelFile = self::insertBetween($kernelFile, "'web' => [", "]", $middleware);
            self::save($kernelFile);
        }
    }

    public static function addMiddlewareToApiGroup($middleware)
    {
        $kernelFile = self::read();
        if (!str_contains($kernelFile, $middleware)) {
            $kernelFile = self::insertBetween($kernelFile, "'api' => [", "]", $middleware);
            self::save($kernelFile);
        }
    }

    public static function addRouteMiddleware($middleware)
    {
        $kernelFile = self::read();
        if (!str_contains($kernelFile, $middleware)) {
            $kernelFile = self::insertBetween($kernelFile, 'protected $routeMiddleware = [', "]", $middleware);
            self::save($kernelFile);
        }
    }

    /**
     * 向Kernel里添加一个middleware
     * @param $middleware
     */
    public static function addMiddleware($middleware)
    {
        $kernelFile = self::read();
        if (!str_contains($kernelFile, $middleware)) {
            $kernelFile = self::insertBetween($kernelFile, 'protected $middleware = [', "]", $middleware);
            self::save($kernelFile);
        }
    }

    /**
     * 从Kernel里删除一个middleware
     * @param $middleware
     */
    public static function removeMiddleware($middleware)
    {
        $kernelFile = self::read();
        if (str_contains($kernelFile, $middleware)) {
            $kernelFile = preg_replace('^.*' . $middleware . '.*$', '', $kernelFile);
            self::save($kernelFile);
        }
    }
}