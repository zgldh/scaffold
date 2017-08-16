<?php namespace zgldh\Scaffold\Installer;

use zgldh\Scaffold\Installer\Model\FieldDefinition;
use zgldh\Scaffold\Installer\Model\ModelDefinition;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
abstract class ModuleStarter
{
    abstract protected function defineName();

    abstract protected function defineModels();

    /**
     * @param string $name 'some_model_name'
     * @param array $fields
     * @return ModelDefinition
     */
    protected function newModel($name, $fields = [])
    {
        return new ModelDefinition($name, $fields);
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

    public function getModuleNameSpace()
    {
        return dirname(static::class);
    }

    public function getModuleFolder()
    {
        $ref = new \ReflectionClass(static::class);
        return dirname($ref->getFileName());
    }

    public function getModels()
    {
        return $this->defineModels();
    }

    public function getModuleName()
    {
        return $this->defineName();
    }
}