<?php namespace zgldh\Scaffold\Installer;

use zgldh\Scaffold\Installer\Model\FieldDefinition;
use zgldh\Scaffold\Installer\Model\ModelDefinition;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
abstract class ModuleStarter
{
    abstract protected function defineTitle();

    abstract protected function defineModels();

    /**
     * @param string $name 'some_model_name'
     * @param array $fields
     * @return ModelDefinition
     */
    protected function newModel($name, $fields = [])
    {
        $model = new ModelDefinition($name, $fields);
        if (snake_case($name) !== snake_case($this->getModuleName())) {
            $model->route($this->getModuleName() . '/' . $name);
        }
        return $model;
    }

    /**
     * @param string $name 'some_field_name'
     * @param string $fieldType
     * @return FieldDefinition
     */
    protected function newField($name = '', $fieldType = 'string')
    {
        return new FieldDefinition($name, $fieldType);
    }

    /**
     * Get current defining module namespace. 'Modules/Blog'
     * @return string
     */
    public function getModuleNameSpace()
    {
        return dirname(static::class);
    }

    /**
     * Get current defining module name. 'Blog'
     * @return string
     */
    public function getModuleName()
    {
        return basename($this->getModuleNameSpace());
    }

    public function getLanguageNamespace()
    {
        return snake_case($this->getModuleName());
    }

    /**
     * Get the folder path to the defining module root '/var/wwwroot/Modules/Blog'
     * @return string
     */
    public function getModuleFolder()
    {
        $ref = new \ReflectionClass(static::class);
        return dirname($ref->getFileName());
    }

    /**
     * Get the module name. Should be Chinese characters '博客'
     * @return mixed
     */
    public function getModuleTitle()
    {
        return $this->defineTitle();
    }

    /**
     * Get models array
     * @return mixed
     */
    public function getModels()
    {
        return $this->defineModels();
    }
}