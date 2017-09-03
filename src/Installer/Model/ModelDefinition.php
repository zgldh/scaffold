<?php namespace zgldh\Scaffold\Installer\Model;

use zgldh\Scaffold\Installer\HtmlFields\BaseField;
use zgldh\Scaffold\Installer\HtmlFields\Factory;

/**
 * Class ModuleInstaller
 * @package zgldh\Scaffold\Installer
 */
class ModelDefinition
{
    private $table = '';
    private $title = ''; // Should be some Chinese characters
    private $fields = [];
    /**
     * @var array key: field name; value: search type
     */
    private $searches = [];
    private $middleware = '';
    private $softDelete = false;
    private $activityLog = false;
    private $route = '';

    private $PascaleCase = '';
    private $camelCase = '';
    private $snake_case = '';

    /**
     * ModelDefinition constructor.
     * @param string $name 'some_model_name'
     * @param array $fields
     */
    public function __construct($name, $fields = [])
    {
        $this->setPascaleCase($name);
        $this->setCamelCase($name);
        $this->setSnakeCase($name);
        $this->setTable(str_plural($this->getSnakeCase()));
        $this->route($name);

        $this->fields = $fields;
    }

    /**
     * @param string $table
     * @return ModelDefinition
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    public function addField($name = '', $dbType = 'string')
    {
        $field = new FieldDefinition($name, $dbType);
        $this->fields[] = $field;
        return $field;
    }

    /**
     * @param array $fields
     * @return ModelDefinition
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return FieldDefinition
     */
    public function findFieldByName($name)
    {
        return array_first($this->fields, function ($field) use ($name) {
            return $field->getName() === $name;
        });
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $defaultValue = $field->getDefaultValue();
            $defaultValue = $defaultValue == INF ? null : $defaultValue;
            $fields[$field->getName()] = $defaultValue;
        }
        return $fields;
    }

    /**
     * @param string $middleware
     * @return ModelDefinition
     */
    public function setMiddleware($middleware)
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @return array
     */
    public function getSearches()
    {
        $me = $this;
        $searches = [];
        $fields = $this->getFields();
        foreach ($fields as $field) {
            /**
             * @var FieldDefinition $field
             */
            if (!$field->isNotSearchable() && $field->isInIndex()) {
                $searches[$field->getName()] = $field->getSearchType();
            }
        }

        foreach ($this->searches as $fieldName => $searchType) {
            $baseField = Factory::getField($searchType);
            $field = $this->findFieldByName($fieldName);
            $baseField->setProperty($fieldName);
            if ($field) {
                $baseField->setField($field);
                $baseField->setLabel($field->getLabel());
            }
            $searches[$fieldName] = $baseField;
        }
        return $searches;
    }

    /**
     * @param array $searches
     * @return ModelDefinition
     */
    public function setSearches($searches)
    {
        $this->searches = $searches;
        return $this;
    }

    public function addSearch($fieldName, $searchType)
    {
        $this->searches[$fieldName] = $searchType;
    }

    /**
     * @return bool
     */
    public function isSoftDelete()
    {
        return $this->softDelete;
    }

    /**
     * @param bool $softDelete
     * @return ModelDefinition
     */
    public function softDelete($softDelete = true)
    {
        $this->softDelete = $softDelete;
        return $this;
    }

    /**
     * @return string
     */
    public function getPascaleCase()
    {
        return $this->PascaleCase;
    }

    /**
     * @param string $PascaleCase
     */
    public function setPascaleCase($PascaleCase)
    {
        $target = ucfirst(camel_case($PascaleCase));
        $this->PascaleCase = $target;
    }

    /**
     * @return string
     */
    public function getCamelCase()
    {
        return $this->camelCase;
    }

    /**
     * @param string $camelCase
     */
    public function setCamelCase($camelCase)
    {
        $this->camelCase = camel_case($camelCase);
    }

    /**
     * @return string
     */
    public function getSnakeCase()
    {
        return $this->snake_case;
    }

    /**
     * @return string
     */
    public function getKebabCase()
    {
        return kebab_case($this->snake_case);
    }

    /**
     * @param string $snake_case
     */
    public function setSnakeCase($snake_case)
    {
        $this->snake_case = snake_case($snake_case);
    }

    /**
     * 为本 Model 添加基础 ActivityLog 支持
     * @param bool $activityLog
     * @return ModelDefinition
     */
    public function useActivityLog($activityLog = true)
    {
        $this->activityLog = $activityLog;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActivityLog()
    {
        return $this->activityLog;
    }

    /**
     * @param string $route 'blog', 'my_works', 'goods_to_sell'
     * @return ModelDefinition
     */
    public function route($route)
    {
        $partials = preg_split('/\//', $route);
        $partials = array_map(function ($partial) {
            return snake_case($partial);
        }, $partials);
        $this->route = join('/', $partials);
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * 得到路由资源名
     * @return string
     */
    public function getRouteResourceName()
    {
        $route = $this->getRoute();
        $resourceName = str_after($route, '/');
        return $resourceName;
    }

    /**
     * @param string $title
     * @return ModelDefinition
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTableSchema()
    {
        $schema = [];
        $fields = $this->getFields();
        foreach ($fields as $field) {
            /**
             * @var FieldDefinition $field
             */
            $schema[] = $field->getSchema();
        }
        if ($this->isSoftDelete()) {
            $schema[] = "deleted_at:softDeletes";
        }

        return join(',', $schema);
    }

    /**
     * 生成 ListPage.vue 的搜索表单 HTML
     */
    public function generateListSearchForm()
    {
        $form = <<<EOT
<el-form :inline="true" :model="searchForm" ref="searchForm">

EOT;
        $searches = $this->getSearches();
        foreach ($searches as $fieldName => $searchType) {
            /**
             * @var $searchType BaseField
             */
            $fieldHtml = $searchType->searchHtml();
            $form .= $fieldHtml . "\n";
        }

        $form .= <<<EOT
  <el-form-item>
    <el-button-group>
      <el-button type="primary" @click="onSubmitSearch">{{\$t('scaffold.terms.search_submit')}}</el-button>
      <el-button type="button" @click="onResetSearch">{{\$t('scaffold.terms.search_reset')}}</el-button>
    </el-button-group>
  </el-form-item>
</el-form>
EOT;

        return $form;
    }

    /**
     * 生成 EditorPage.vue 的编辑表单 HTML
     */
    public function generateEditorForm()
    {
        $form = <<<EOT
<el-form ref="form" :model="form" label-width="200px" v-loading="loading">
  <el-form-item label="ID" v-if="form.id">
    <el-input v-model="form.id" disabled></el-input>
  </el-form-item>

EOT;
        $fields = $this->getFields();
        foreach ($fields as $field) {
            /**
             * @var $field     FieldDefinition
             * @var $baseField BaseField
             */
            $baseField = $field->getHtmlType();
            $fieldHtml = $baseField->html();
            $form .= $fieldHtml . "\n";
        }

        $form .= <<<EOT
    <el-form-item label="Created At" v-if="form.id">
      <el-input v-model="form.created_at" disabled></el-input>
    </el-form-item>
</el-form>
EOT;

        return $form;
    }

    /**
     * 生成 vue 页面需要的 computed 参数
     */
    public function generateComputes()
    {
        $computes = [];
        foreach ($this->getFields() as $field) {
            $computedCode = $field->getComputedCode();
            if ($computedCode) {
                $computes[] = $computedCode;
            }
        }

        return $computes;
    }
}