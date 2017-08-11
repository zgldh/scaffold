<?php namespace zgldh\Scaffold\Installer\Model;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
class FieldDefinition
{
    private $schema = '';

    private $name = '';
    private $dbType = '';
    private $indexType = null;
    private $nullable = false;
    private $defaultValue = null;

    private $htmlType = 'text';
    private $label = '';
    private $required = false;
    private $validations = '';
    private $sortable = true;
    private $inIndex = false;

    public function __construct($name = '', $dbType = 'text')
    {
        $this->name = $name;
        $this->label = $name;
        $this->dbType = $dbType;
    }

    /**
     * @param string $name
     * @return FieldDefinition
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $schema
     * @return FieldDefinition
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param string $dbType
     * @return FieldDefinition
     */
    public function setDbType($dbType)
    {
        $this->dbType = $dbType;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbType()
    {
        return $this->dbType;
    }

    /**
     * @return null
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @return null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param null $defaultValue
     * @return FieldDefinition
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param bool $nullable
     * @return FieldDefinition
     */
    public function setNullable($nullable = true)
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @param null $indexType
     * @return FieldDefinition
     */
    public function setIndexType($indexType)
    {
        $this->indexType = $indexType;
        return $this;
    }

    /**
     * @param string $htmlType
     * @return FieldDefinition
     */
    public function setHtmlType($htmlType)
    {
        $this->htmlType = $htmlType;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlType()
    {
        return $this->htmlType;
    }

    /**
     * @param string $label
     * @return FieldDefinition
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param bool $required
     * @return FieldDefinition
     */
    public function setRequired($required = true)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param string $validations
     * @return FieldDefinition
     */
    public function setValidations($validations)
    {
        $this->validations = $validations;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * @return FieldDefinition
     */
    public function notSortable()
    {
        $this->sortable = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @param bool $inIndex
     * @return FieldDefinition
     */
    public function setInIndex($inIndex = true)
    {
        $this->inIndex = $inIndex;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInIndex()
    {
        return $this->inIndex;
    }
}