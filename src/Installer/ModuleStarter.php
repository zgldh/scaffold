<?php namespace zgldh\Scaffold\Installer;

use zgldh\Scaffold\Installer\Model\FieldDefinition;
use zgldh\Scaffold\Installer\Model\ModelDefinition;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
abstract class ModuleStarter
{
    abstract function defineName();

    abstract function defineModels();

    protected function newModel($tableName = '', $fields = [])
    {
        return new ModelDefinition($tableName, $fields);
    }

    protected function newField($name = '', $fieldType = 'text')
    {
        return new FieldDefinition($name, $fieldType);
    }
}