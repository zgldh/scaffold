<?php namespace App\Scaffold\Installer\Model;

use zgldh\Scaffold\Installer\HtmlFields\BaseField;
use zgldh\Scaffold\Installer\HtmlFields\Factory;

/**
 * Class ModelDefinition
 * @package App\Scaffold\Installer
 */
class ModelDefinition
{
    private $moduleName = '';
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

    private $vueEditorComponents = [];

    /**
     * ModelDefinition constructor.
     * @param string $name 'some_model_name' singular
     * @param null $tableName
     */
    public function __construct($name, $tableName = null)
    {
        $this->setPascaleCase($name);
        $this->setCamelCase($name);
        $this->setSnakeCase($name);
        $this->route($name);

        if ($tableName) {
            $this->setTable($tableName);
        } else {
            $this->setTable(str_plural($this->getSnakeCase()));
        }
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

    /**
     * Get the model name
     * @return string
     */
    public function getModelName()
    {
        return ucfirst($this->getCamelCase());
    }

    public function addField($name = '', $dbType = 'string')
    {
        $field = new Field($name, $dbType);
        $field->setModel($this);
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
        foreach ($this->fields as $field) {
            $field->setModel($this);
        }
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
     * @return Field
     */
    public function findFieldByName($name)
    {
        $field = array_first($this->fields, function ($field) use ($name) {
            return $field->getName() === $name;
        });
        if (!$field) {
            $commonFieldClass = __NAMESPACE__ . '\CommonFields\\' . ucfirst(camel_case($name));
            if (class_exists($commonFieldClass)) {
                /**
                 * @var Field $field
                 */
                $field = new $commonFieldClass;
                $field->setModel($this);
            } else {
                dd($commonFieldClass . ' not exist.');
            }
        }
        return $field;
    }

    /**
     * @return array
     */
    public function getFactories()
    {
        $factories = [];
        foreach ($this->fields as $field) {
            if ($field->isNotTableField()) {
                continue;
            }
            $factory = $field->getFactory();
            $factories[$field->getName()] = $factory;
        }
        return $factories;
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

    public function getFieldLabels($locale = 'zh-CN')
    {
        $labels = [];
        foreach ($this->fields as $field) {
            /**
             * @var Field $field
             */
            $key = $field->getName();
            if ($locale === 'en') {
                $value = $field->getReadingName();
            } else {
                $value = $field->getLabel();
            }
            $labels[$key] = $value;
        }
        return $labels;
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
             * @var Field $field
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
    public function getRoute($with = [])
    {
        $route = $this->route;
        if ($with) {
            $route .= '?_with=' . join(',', $with);
        }
        return $route;
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
     * 前端路由前缀部分
     * @return string
     */
    public function getFrontEndRoutePrefix()
    {
        $moduleSnakeCase = snake_case($this->getModuleName());
        $modelSnakeCase = $this->getSnakeCase();
        return "/{$moduleSnakeCase}/{$modelSnakeCase}";
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
             * @var Field $field
             */
            if ($field->isNotTableField()) {
                continue;
            }
            $schema[] = $field->getSchema();
        }
        if ($this->isSoftDelete()) {
            $schema[] = "deleted_at:softDeletes";
        }

        return join(',', $schema);
    }

    /**
     * Get Model language set term
     * @return string
     */
    public function getModelLang()
    {
        $langTerm = $this->getSnakeCase();
        return $langTerm;
    }

    /**
     * @param string $moduleName
     * @return ModelDefinition
     */
    public function setModuleName(string $moduleName): ModelDefinition
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        $relations = [];
        foreach ($this->getFields() as $field) {
            /**
             * @var Field $field
             */
            if ($relation = $field->getRelationship()) {
                $relations[] = $relation;
            }
        }
        return $relations;
    }

    /**
     * 得到当前 model 各种 relation 的名字
     * @return array
     */
    public function getRelationNames()
    {
        $names = [];
        foreach ($this->getFields() as $field) {
            /**
             * @var Field $field
             */
            $relationshipName = $field->getRelationshipName($names);
            if ($relationshipName) {
                $names[] = $relationshipName;
            }
        }
        return $names;
    }

    /**
     * getVueEditorComponents
     */
    public function getVueEditorComponents()
    {
        return $this->vueEditorComponents;
    }

    /**
     * @param array $vueEditorComponents
     */
    public function setVueEditorComponents(array $vueEditorComponents)
    {
        $this->vueEditorComponents = $vueEditorComponents;
    }

    /**
     * Add a vue editor component
     * @param $name
     * @param $path
     */
    public function addVueEditorComponents($name, $path)
    {
        $this->vueEditorComponents[$name] = $path;
    }
}