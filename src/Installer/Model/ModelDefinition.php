<?php namespace zgldh\Scaffold\Installer\Model;

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

    public function addField($name = '', $fieldType = 'string')
    {
        $field = new FieldDefinition($name, $fieldType);
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
        $searches = [];
        $fields = $this->getFields();
        foreach ($fields as $field) {
            if (!$field->isNotSearchable()) {
                $searches[$field->getName()] = $field->getSearchType();
            }
        }
        $searches = array_merge($searches, $this->searches);
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
     * TODO 生成 ListPage.vue 的搜索表单 HTML
     */
    public function generateListSearchForm()
    {
        return '<p>TODO 生成 ListPage.vue 的搜索表单 HTML</p>';
    }

    /**
     * 生成 ListPage.vue 的 Datatable 数据列 HTML
     */
    public function generateDatatableColumns()
    {
        $html = <<<EOT
              <el-table-column
                      fixed
                      type="selection"
                      width="55">
              </el-table-column>

EOT;
        $fields = $this->getFields();
        foreach ($fields as $field) {
            /**
             * @var FieldDefinition $field
             */
            if (!$field->isInIndex()) {
                continue;
            }
            $prop = $field->getName();
            $label = $field->getLabel();
            $sortable = $field->isSortable() ? 'sortable="custom"' : ':sortable="false"';
            $searchable = $field->isNotSearchable() ? 'searchable="false"' : 'searchable="true"';
            $html .= <<<EOT
              <el-table-column
                      prop="{$prop}"
                      label="{$label}"
                      {$sortable}
                      {$searchable}
                      show-overflow-tooltip>
              </el-table-column>

EOT;
        }

        $html .= <<<EOT
              <el-table-column
                      fixed="right"
                      label="操作"
                      width="120">
                <template scope="scope">
                  <el-button-group>
                    <el-button @click="onEditClick(scope.row,scope.column,scope.\$index,scope.store)" type="default"
                               size="small" icon="edit" title="编辑"></el-button>
                    <el-button @click="onDeleteClick(scope.row,scope.column,scope.\$index,scope.store)" type="danger"
                               size="small" icon="delete" title="删除"></el-button>
                  </el-button-group>
                </template>
              </el-table-column>

EOT;
        return $html;
    }

    /**
     * TODO 生成 EditorPage.vue 的编辑表单 HTML
     */
    public function generateEditorForm()
    {
        return '<p>TODO 生成 EditorPage.vue 的编辑表单 HTML</p>';
    }
}