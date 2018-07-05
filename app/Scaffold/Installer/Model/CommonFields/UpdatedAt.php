<?php namespace App\Scaffold\Installer\Model\CommonFields;

use App\Scaffold\Installer\HtmlFields\BaseField;
use App\Scaffold\Installer\Model\Field;
use App\Scaffold\Installer\Model\ModelDefinition;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
class UpdatedAt extends Field
{
    /**
     * 字段名 snake_case
     * @var string
     */
    private $name = 'updated_at';
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
    private $defaultValue = INF;

    /**
     * 界面控件类型
     * @var string|BaseField
     */
    private $htmlType = 'text';
    /**
     * 界面控件label名
     * @var string
     */
    private $label = 'Updated At';
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
     * @var ModelDefinition
     */
    private $model = null;


    /**
     * FieldDefinition constructor.
     * @param string $name 'some_field_name'
     * @param string $dbType
     */
    public function __construct($name = 'updated_at', $dbType = 'timestamp')
    {
        parent::__construct($name, $dbType);
    }

    public function getFieldLang($forFrontEnd = false)
    {
        $lang = 'global.fields.updated_at';
        $lang = $forFrontEnd ? $this->decorateFieldLangForVue($lang) : $lang;
        return $lang;
    }
}