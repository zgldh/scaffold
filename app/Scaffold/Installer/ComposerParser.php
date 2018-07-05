<?php namespace App\Scaffold\Installer;

use Illuminate\Support\ServiceProvider;

class ComposerParser
{
    private $composer = null;
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
        $this->composer = json_decode($content, true);
        $this->path = $path;
    }

    public function save($path = null)
    {
        $savePath = $this->path;
        if ($path) {
            $savePath = $path;
        }
        $data = json_encode($this->composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return file_put_contents($savePath, $data);
    }

    public function getValue($trunkName, $branchName)
    {
        $key = $trunkName;
        if ($branchName !== null) {
            $key .= '.' . $branchName;
        }
        return array_get($this->composer, $key);
    }

    public function setValue($key, $value)
    {
        return array_set($this->composer, $key, $value);
    }

    public function getScript($type = null)
    {
        return $this->getValue('scripts', $type);
    }

    public function setScript($type, $value)
    {
        return $this->setValue('scripts.' . $type, $value);
    }

    public function getConfig($type = null)
    {
        return $this->getValue('config', $type);
    }

    public function setConfig($type, $value)
    {
        return $this->setValue('config.' . $type, $value);
    }

    public function getAutoload($type = null)
    {
        return $this->getValue('autoload', $type);
    }

    public function setAutoload($type, $value)
    {
        return $this->setValue('autoload.' . $type, $value);
    }

    public function getAutoloadPsr4($type = null)
    {
        return $this->getValue('autoload.psr-4', $type);
    }

    public function setAutoloadPsr4($type, $value)
    {
        return $this->setValue('autoload.psr-4.' . $type, $value);
    }

    public function getRequire($type = null)
    {
        return $this->getValue('require', $type);
    }

    public function setRequire($type, $value)
    {
        return $this->setValue('require.' . $type, $value);
    }

    public function getRequireDev($type = null)
    {
        return $this->getValue('require-dev', $type);
    }

    public function setRequireDev($type, $value)
    {
        return $this->setValue('require-dev.' . $type, $value);
    }
}