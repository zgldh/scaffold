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
    private $nullable = true;
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

    /**
     * 是否在列表中搜索
     * @var mixed
     */
    private $searchType = 'inherent';

    /**
     * 本字段和别的Model有什么关系
     * @var mixed
     */
    private $relationship = null;


    /**
     * FieldDefinition constructor.
     * @param string $name 'some_field_name'
     * @param string $dbType
     */
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
        if ($defaultValue === null) {
            $this->nullable();
            $this->required(false);
        }
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
        if ($required) {
            $this->nullable(false);
        }
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

    /**
     * @param  mixed $searchType
     * @return FieldDefinition
     */
    public function noSearch()
    {
        $this->searchType = false;
        return $this;
    }

    /**
     * @param  mixed $searchType
     * @return FieldDefinition
     */
    public function searchType($searchType)
    {
        $this->searchType = $searchType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearchType()
    {
        return $this->searchType;
    }

    /**
     * @return mixed
     */
    public function getRelationship()
    {
        return json_decode($this->relationship);
    }

    /**
     * @param mixed $relationship
     * @return FieldDefinition
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
        return $this;
    }


    /**
     * Define a one-to-many relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @param  string $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a has-many-through relationship.
     *
     * @param  string $related
     * @param  string $through
     * @param  string|null $firstKey
     * @param  string|null $secondKey
     * @param  string|null $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string $related
     * @param  string $name
     * @param  string $type
     * @param  string $id
     * @param  string $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string $related
     * @param  string $table
     * @param  string $foreignKey
     * @param  string $relatedKey
     * @param  string $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $relatedKey = null, $relation = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string $related
     * @param  string $name
     * @param  string $table
     * @param  string $foreignKey
     * @param  string $relatedKey
     * @param  bool $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $relatedKey = null, $inverse = false)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @param  string $related
     * @param  string $name
     * @param  string $table
     * @param  string $foreignKey
     * @param  string $relatedKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphedByMany($related, $name, $table = null, $foreignKey = null, $relatedKey = null)
    {
        $this->relationship = json_encode(func_get_args());
    }


    /**
     * Define a one-to-one relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @param  string $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string $related
     * @param  string $name
     * @param  string $type
     * @param  string $id
     * @param  string $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @param  string $ownerKey
     * @param  string $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $this->relationship = json_encode(func_get_args());
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string $name
     * @param  string $type
     * @param  string $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function morphTo($name = null, $type = null, $id = null)
    {
        $this->relationship = json_encode(func_get_args());
    }
}