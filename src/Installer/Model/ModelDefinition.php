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
        return $this->searches;
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
     * @param string $route 'blog', 'my-works', 'goods-to-sell'
     * @return ModelDefinition
     */
    public function route($route)
    {
        $partials = preg_split('/\//', $route);
        $partials = array_map(function ($partial) {
            return kebab_case($partial);
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
}