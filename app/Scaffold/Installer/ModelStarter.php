<?php namespace App\Scaffold\Installer;

use App\Scaffold\Installer\Model\Field;
use App\Scaffold\Installer\Model\ModelDefinition;

/**
 * Class ModelStarter
 * @package App\Scaffold\Installer
 */
abstract class ModelStarter
{
    protected $defaultLocation = 'zh_CN';

    /**
     * ModelNameLikeThis
     * @return string
     */
    abstract protected function modelName();

    /**
     * table_name_like_this
     * @return string
     */
    abstract protected function tableName();

    /**
     * [
     *      new Field()->label()->index()->required()->belongsTo().....,
     *      new Field()->label()->nullable().....
     * ]
     * @return array
     */
    abstract protected function fields();

    /**
     * @return boolean
     */
    abstract protected function needActivityLog();

    /**
     * @return boolean
     */
    abstract protected function isSoftDelete();

    /**
     * @var FakerFace
     */
    protected $faker = null;

    /**
     * @return ModelDefinition
     */
    public function getModel()
    {
        $this->faker = new FakerFace();

        $modelDefinition = new ModelDefinition($this->modelName(), $this->tableName());
        $modelDefinition->setModuleName($this->getModuleName());
        $modelDefinition->setMiddleware(config('scaffold.default_middleware'));
        $modelDefinition->useActivityLog($this->needActivityLog());
        $modelDefinition->softDelete($this->isSoftDelete());
        $modelDefinition->setFields($this->fields());
        return $modelDefinition;
    }

    public function getModelName()
    {
        return $this->modelName();
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
        return substr(static::class, 0, -strlen(class_basename(static::class)) - 1);
    }

    /**
     * Get current defining module name. 'Blog'
     * @return string
     */
    public function getModuleName()
    {
        return class_basename($this->getModuleNameSpace());
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
     * Get models array
     * @return mixed
     */
    public function getModels()
    {
        return [$this->getModel()];
    }
}