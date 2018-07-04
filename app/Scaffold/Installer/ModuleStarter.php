<?php namespace App\Scaffold\Installer;

use App\Scaffold\Installer\Model\Field;
use App\Scaffold\Installer\Model\ModelDefinition;

/**
 * Class ModuleStarter
 * @package App\Scaffold\Installer
 * @deprecated
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
        $model->setModuleName($this->getModuleName());
        if (snake_case($name) !== snake_case($this->getModuleName())) {
            $model->route($this->getModuleName() . '/' . $name);
        }
        return $model;
    }

    /**
     * @param string $name 'some_field_name'
     * @param string $fieldType
     * @return Field
     */
    protected function newField($name = '', $fieldType = 'string')
    {
        return new Field($name, $fieldType);
    }

    /**
     * Get current defining module namespace. 'Modules/Blog'
     * @return string
     */
    public function getModuleNameSpace()
    {
        dd(static::class);
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