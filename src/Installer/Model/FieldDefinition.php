<?php namespace zgldh\Scaffold\Installer\Model;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
class FieldDefinition
{
    private $schema = '';

    /**
     * 字段名
     * @var string
     */
    private $name = '';
    /**
     * 数据库字段类型
     * @var string
     */
    private $dbType = '';
    /**
     * 索引类型 'index','unique'
     * @var null
     */
    private $indexType = null;
    /**
     * 数据库是否可以为null
     * @var bool
     */
    private $nullable = false;
    /**
     * 数据库/界面默认值
     * @var null
     */
    private $defaultValue = null;

    /**
     * 界面控件类型
     * @var string
     */
    private $htmlType = 'text';
    /**
     * 界面控件label名
     * @var string
     */
    private $label = '';
    /**
     * 是否必填
     * @var bool
     */
    private $required = false;
    /**
     * Laravel validate 验证机制
     * @var string
     */
    private $validations = '';
    /**
     * 是否可以排序
     * @var bool
     */
    private $sortable = true;
    /**
     * 是否显示在列表页
     * @var bool
     */
    private $inIndex = false;

    public function __construct($name = '', $dbType = 'string')
    {
        $this->name = $name;
        $this->label = $name;
        $this->dbType = $dbType;
    }

    /**
     * @param string $name
     * @return FieldDefinition
     */
    public function name($name)
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
    public function schema($schema)
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
    public function dbType($dbType)
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
    public function defaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param bool $nullable
     * @return FieldDefinition
     */
    public function nullable($nullable = true)
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @param null $indexType
     * @return FieldDefinition
     */
    public function index($indexType)
    {
        $this->indexType = $indexType;
        return $this;
    }

    /**
     * @param string $htmlType
     * @return FieldDefinition
     */
    public function htmlType($htmlType)
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
    public function label($label)
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
    public function required($required = true)
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
    public function validations($validations)
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
    public function inIndex($inIndex = true)
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