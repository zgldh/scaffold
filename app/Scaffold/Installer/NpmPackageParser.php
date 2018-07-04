<?php namespace App\Scaffold\Installer;

use Illuminate\Support\ServiceProvider;

class NpmPackageParser
{
    private $package = null;
    private $path = null;

    public function __construct($path = null)
    {
        if ($path) {
            $this->load($path);
        }
    }

    public function load($path)
    {
        $content = file_get_contents($path);
        $this->package = json_decode($content, true);
        $this->path = $path;
    }

    public function save($path = null)
    {
        $savePath = $this->path;
        if ($path) {
            $savePath = $path;
        }
        $data = json_encode($this->package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return file_put_contents($savePath, $data);
    }

    public function getValue($trunkName, $branchName)
    {
        $key = $trunkName;
        if ($branchName !== null) {
            $key .= '.' . $branchName;
        }
        return array_get($this->package, $key);
    }

    public function setValue($key, $value)
    {
        return array_set($this->package, $key, $value);
    }

    public function getScript($type = null)
    {
        return $this->getValue('scripts', $type);
    }

    public function setScript($type, $value)
    {
        return $this->setValue('scripts.' . $type, $value);
    }

    public function getDevDependencies($dependency = null)
    {
        return $this->getValue('devDependencies', $dependency);
    }

    public function setDevDependencies($dependency, $value)
    {
        return $this->setValue('devDependencies.' . $dependency, $value);
    }

    public function getDependencies($dependency = null)
    {
        return $this->getValue('dependencies', $dependency);
    }

    public function setDependencies($dependency, $value)
    {
        return $this->setValue('dependencies.' . $dependency, $value);
    }
}