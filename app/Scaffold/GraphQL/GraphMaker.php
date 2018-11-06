<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 10/09/2018
 * Time: 19:25
 */

namespace App\Scaffold\GraphQL;


use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GraphMaker
{
    private static $types = [];
    private static $schemas = [];

    public static function addSchema($config, $schema = 'default')
    {
        if (!config('scaffold.enable_graph_ql', true)) {
            return;
        }
        if (isset(self::$schemas[$schema]) && self::$schemas[$schema]) {
            self::$schemas[$schema] = array_merge_recursive(self::$schemas[$schema], $config);
        } else {
            self::$schemas[$schema] = $config;
        }
    }

    public static function addType($typeClass, $typeName)
    {
        if (!config('scaffold.enable_graph_ql', true)) {
            return;
        }
        self::$types[$typeName] = $typeClass;
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return self::$types;
    }

    /**
     * @return array
     */
    public static function getSchemas(): array
    {
        return self::$schemas;
    }

    public static function cleanAll()
    {
        self::$types = [];
        self::$schemas = [];
    }

    private static $parsedModels = [];

    public static function getFilterType($modelClass, $relations = [])
    {
        $parsedModel = self::getParsedModel($modelClass, $relations);
        return $parsedModel['FilterType'];
    }

    public static function getModelObjectType($className, $relations = [])
    {
        $parsedModel = self::getParsedModel($className, $relations);
        return $parsedModel['ObjectType'];
    }

    private static function getParsedModel($className, $relations = [])
    {
        if (!isset(self::$parsedModels[$className])) {
            self::$parsedModels[$className] = self::parseModel($className, $relations);
        }
        return self::$parsedModels[$className];
    }

    private static function parseModel($className, $relations = [])
    {
        /** @var Model $model */
        $model = new $className;
        $parsedModel = [];
        $parsedModel['FilterType'] = new InputObjectType([
            'name'        => self::unifySnakeCaseClassName($model, '_filter'),
            'fields'      => function () use ($model, $relations) {
                $visible = $model->getVisible();
                $hidden = $model->getHidden();
                $casts = $model->getCasts();

                $filterFields = [[
                    'name' => 'id',
                    'type' => self::getFieldFilterType()
                ]];
                foreach (self::getModelAvailableFields($casts, $visible, $hidden) as $attribute) {
                    $filterFields[] = [
                        'name' => $attribute,
                        'type' => self::getFieldFilterType()
                    ];;
                }

                foreach (self::getRelationshipFields('FilterType', $model, $relations, $visible, $hidden) as $field) {
                    $filterFields[] = $field;
                }

                return $filterFields;
            },
            'description' => get_class($model)
        ]);
        $parsedModel['ObjectType'] = new ObjectType([
            'name'        => self::unifySnakeCaseClassName($model, '_object'),
            'fields'      => function () use ($model, $relations) {
                $visible = $model->getVisible();
                $hidden = $model->getHidden();
                $casts = $model->getCasts();

                $objectFields = [[
                    'name' => 'id',
                    'type' => Type::int()
                ]];
                foreach (self::getModelAvailableFields($casts, $visible, $hidden) as $attribute) {
                    $objectFields[] = [
                        'name' => $attribute,
                        'type' => self::getModelFieldOutputType($attribute, $casts)
                    ];;
                }

                foreach (self::getRelationshipFields('ObjectType', $model, $relations, $visible, $hidden) as $field) {
                    $objectFields[] = $field;
                }

                return $objectFields;
            },
            'description' => get_class($model)
        ]);

        return $parsedModel;
    }

    private static function getModelAvailableFields($casts, $visible = [], $hidden = [])
    {
        foreach (array_keys($casts) as $attribute) {
            if ($visible && !in_array($attribute, $visible)) {
                continue;
            } else if (in_array($attribute, $hidden)) {
                continue;
            } else {
                yield $attribute;
            }
        }
    }

    private static function getRelationshipFields($type, $model, $relations, $visible = [], $hidden = [])
    {
        foreach ($relations as $relation) {
            if ($visible && !in_array($relation, $visible)) {
                continue;
            } else if (in_array($relation, $hidden)) {
                continue;
            } else if (method_exists($model, $relation)) {
                $relatedClassName = null;
                $relationship = $model->$relation();
                if (method_exists($relationship, 'getMorphType')) {
                    // Morph relationship
                    switch (get_class($relationship)) {
                        case MorphOne::class:
                            $relatedClassName = get_class($relationship->getRelated());
                            break;
                        default:
                            // Not support relationship
                            \Log::info(__FUNCTION__, ['not support yet', $relationship]);
                            continue;
                            break;
                    }
                } else {
                    $relatedClassName = get_class($relationship->getRelated());
                }
                if (!$relatedClassName) {
                    continue;
                }
                $parsedRelatedModel = self::getParsedModel($relatedClassName, []);
                $field = [
                    'name' => $relation,
                    'type' => $parsedRelatedModel[$type]
                ];
                yield $field;
            }
        }
    }

    public static function queryResolver($query, $root, $args)
    {
        if (isset($args['filter'])) {
            self::applyFilter($query, $root, $args['filter']);
        }
        $result = $query->get();
        return $result;
    }

    private static function unifySnakeCaseClassName($className, $tail = '')
    {
        return class_basename($className) . $tail . '_' . substr(crc32($className), 0, 4);
    }

    /**
     * 查询条件过滤器。 不同的字段之间是 AND 关系，字段内不同操作符是 AND 关系
     * @param $query
     * @param $root
     * @param $filter
     */
    private static function applyFilter($query, $root, $fields)
    {
        foreach ($fields as $field => $filters) {
            $columnNames = explode('.', $field);
            $query->where(function ($q) use ($columnNames, $filters) {
                foreach ($filters as $operator => $arguments) {
                    self::advanceSearch($q, $columnNames, $operator, $arguments);
                }
            });
        }
    }

    private static function advanceSearch($query, $columnNames, $operator, $arguments)
    {
        if (sizeof($columnNames) == 1) {
            $columnName = $columnNames[0];
            switch ($operator) {
                case 'eq':
                    $query->where($columnName, '=', $arguments);
                    break;
                case 'neq':
                    $query->where($columnName, '<>', $arguments);
                    break;
                case 'gt':
                    $query->where($columnName, '>', $arguments);
                    break;
                case 'lt':
                    $query->where($columnName, '<', $arguments);
                    break;
                case 'egt':
                    $query->where($columnName, '>=', $arguments);
                    break;
                case 'elt':
                    $query->where($columnName, '<=', $arguments);
                    break;
                case 'in':
                    $query->whereIn($columnName, $arguments);
                    break;
                case 'notIn':
                    $query->whereNotIn($columnName, $arguments);
                    break;
                case 'between':
                    $query->whereBetween($columnName, $arguments);
                    break;
                case 'notBetween':
                    $query->whereNotBetween($columnName, $arguments);
                    break;
                case 'like':
                    $query->where($columnName, 'like', $arguments);
                    break;
                case 'null':
                    $query->whereNull($columnName);
                    break;
                case 'notNull':
                    $query->whereNotNull($columnName);
                    break;

                default:
                    \Log::error('Bad operator: ' . $operator . ' in field: ' . $columnName);
            }
        } else {
            $columnName = array_shift($columnNames);
            $query->whereHas($columnName, function ($q) use ($columnNames, $operator, $arguments) {
                self::advanceSearch($q, $columnNames, $operator, $arguments);
            });
        }
    }

    private static function getModelFieldOutputType($attribute, $casts)
    {
        $cast = isset($casts[$attribute]) ? $casts[$attribute] : 'string';
        switch ($cast) {
            case 'int':
            case 'integer':
                return Type::int();
                break;
            case 'real':
                return Type::float();
                break;
            case 'float':
                return Type::float();
                break;
            case 'double':
                return Type::string();
                break;
            case 'string':
                return Type::string();
                break;
            case 'boolean':
                return Type::boolean();
                break;
            case 'object':
                return Type::string();
                break;
            case 'array':
                return Type::listOf(Type::string());
                break;
            case 'collection':
                return Type::listOf(Type::string());
                break;
            case 'date':
                return Type::string();
                break;
            case 'datetime':
                return Type::string();
                break;
            case 'timestamp':
                return Type::string();
                break;
            default:
                \Log::error('Bad cast type: ' . $cast . ' on attribute: ' . $attribute);
                return Type::string();
                break;
        }
    }

    private static function getFieldFilterType()
    {
        return \GraphQL::type('FieldFilter');
    }

}