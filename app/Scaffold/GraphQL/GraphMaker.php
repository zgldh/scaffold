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
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL\Deferred;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class GraphMaker
{
    const OBJECT_TYPE = 'ObjectType';
    const FILTER_TYPE = 'FilterType';

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

    /**
     * For GraphQL query args() function.
     * @param $modelClass
     * @param array $with
     * @return array
     */
    public static function getQueryArgumentsDefinitionArray($modelClass, $with = [])
    {
        return [
            'filter'      => ['name' => 'filter',
                              'type' => GraphMaker::getFilterType($modelClass, $with),
            ],
            'queryOption' => ['name' => 'queryOption',
                              'type' => \GraphQL::type('QueryOptions')
            ],
        ];
    }

    private static $parsedModels = [];

    public static function getFilterType($modelClass, $relations = [])
    {
        $parsedModel = self::getParsedModel($modelClass, $relations);
        return $parsedModel[self::FILTER_TYPE];
    }

    public static function getModelObjectType($className, $relations = [])
    {
        $parsedModel = self::getParsedModel($className, $relations);
        return $parsedModel[self::OBJECT_TYPE];
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
        $parsedModel[self::FILTER_TYPE] = new InputObjectType([
            'name'        => self::unifySnakeCaseClassName($model, '_filter'),
            'fields'      => function () use ($model, $relations) {
                $visible = $model->getVisible();
                $hidden = $model->getHidden();
                $casts = $model->getCasts();

                $filterFields = [[
                    'name' => 'id',
                    'type' => self::getFieldFilterType('int')
                ]];
                foreach (self::getModelAvailableFields($casts, $visible, $hidden) as $attribute) {
                    $filterFields[] = [
                        'name' => $attribute,
                        'type' => self::getFieldFilterType($casts[$attribute])
                    ];;
                }

                foreach (self::getRelationshipFields(self::FILTER_TYPE, $model, $relations, $visible, $hidden) as $field) {
                    $filterFields[] = $field;
                }

                return $filterFields;
            },
            'description' => get_class($model)
        ]);
        $parsedModel[self::OBJECT_TYPE] = new ObjectType([
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
                    ];
                }

                foreach (self::getRelationshipFields(self::OBJECT_TYPE, $model, $relations, $visible, $hidden) as $field) {
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

    private static $relationshipIsMany = [
        BelongsTo::class      => false,
        BelongsToMany::class  => true,
        HasMany::class        => true,
        HasManyThrough::class => true,
        HasOne::class         => false,
        MorphMany::class      => true,
        MorphOne::class       => false,
        MorphPivot::class     => false,
        MorphTo::class        => false,
        MorphToMany::class    => true,
    ];

    /**
     * @param $type self::FilterType | self::ObjectType
     * @param $model
     * @param $relations
     * @param array $visible
     * @param array $hidden
     * @return \Generator
     */
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
                $relatedClassName = get_class($relationship->getRelated());
                if (!$relatedClassName) {
                    continue;
                }
                $isToMany = self::$relationshipIsMany[get_class($relationship)];
                $parsedRelatedModel = self::getParsedModel($relatedClassName, []);
                $field = [
                    'name' => $relation
                ];
                if ($type === self::FILTER_TYPE) {
                    $field['type'] = $parsedRelatedModel[$type];
                } else {
                    $field['type'] = $isToMany ? Type::listOf($parsedRelatedModel[$type]) : $parsedRelatedModel[$type];
                }
                yield $field;
            }
        }
    }

    public static function queryResolver($query, $root, $args, $context, ResolveInfo $info)
    {
        $filterOnWith = @$args['filterOnWith'] === true;
        $relationships = self::getQueryRelationshipFields($info, $filterOnWith ? $args['filter'] : []);
        if ($relationships) {
            $query->with($relationships);
        }
        if (isset($args['filter'])) {
            self::applyFilter($query, $root, $args['filter']);
        }
        $result = $query->get();
        return $result;
    }

    private static function getQueryRelationshipFields(ResolveInfo $info, $filter = [])
    {
        $relationships = self::getArrayKeysForNotEmptyItem(
            $info->getFieldSelection(config('graphql.security.query_max_depth', 5)),
            '',
            $filter);
        return $relationships;
    }

    private static function getArrayKeysForNotEmptyItem($arr, $rootRelationName = '', $filter = [])
    {
        $keys = [];
        foreach ($arr as $key => $value) {
            if (!is_array($value)) {
                // Skip none array
                continue;
            }
            $innerKeys = self::getArrayKeysForNotEmptyItem($value, $key, $filter);
            if ($innerKeys) {
                $keys = array_merge($keys, $innerKeys);
            } else {
                $relationKey = $rootRelationName ? ($rootRelationName . '.' . $key) : $key;
                if ($filter) {
                    $filterOperators = data_get($filter, $relationKey);
                    $keys[$relationKey] = function ($query) use ($filterOperators) {
                        foreach ($filterOperators as $field => $filters) {
                            $columnNames = [$field];
                            $query->where(function ($q) use ($columnNames, $filters) {
                                foreach ($filters as $operator => $arguments) {
                                    self::advanceSearch($q, $columnNames, $operator, $arguments);
                                }
                            });
                        }
                        $query->limit(100);
                    };
                } else {
                    $keys[] = $relationKey;
                }
            }
        }
        return $keys;
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
        // 将 $fields 规整化。
        $normalizedFields = self::normalizeFilterFields($fields);
        \Log::info(__FUNCTION__, [$fields, $normalizedFields]);
        foreach ($normalizedFields as $field => $filters) {
            $columnNames = explode('.', $field);
            $query->where(function ($q) use ($columnNames, $filters) {
                foreach ($filters as $operator => $arguments) {
                    self::advanceSearch($q, $columnNames, $operator, $arguments);
                }
            });
        }
    }

    /**
     * 将 GraphQL 的 filter 对象转换为用.分割的查询数组
     * @param $fields
     * @return array
     */
    private static function normalizeFilterFields($fields)
    {
        $output = [];
        // Check if $fields are filter operators
        if (FilterType::isFilterOperators(array_keys($fields))) {
            return $output;
        }

        foreach ($fields as $field => $content) {
            $innerFields = self::normalizeFilterFields($content);
            $fieldName = $field;
            if (sizeof($innerFields) > 0) {
                foreach ($innerFields as $innerField => $innerContent) {
                    $output[$fieldName . '.' . $innerField] = $innerContent;
                }
            } else {
                $output[$fieldName] = $content;
            }
        }
        return $output;
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
                    \Log::error('Bad operator: `' . $operator . '` in field: `' . $columnName . '`');
            }
        } else {
            $relationName = array_shift($columnNames);
            $query->whereHas($relationName, function ($q) use ($columnNames, $operator, $arguments) {
                self::advanceSearch($q, $columnNames, $operator, $arguments);
            });
        }
    }

    private static function getModelFieldOutputType($attribute, $casts)
    {
        $cast = isset($casts[$attribute]) ? $casts[$attribute] : 'string';
        return self::resolveGraphQLTypeFromCastType($cast);
    }

    private static function resolveGraphQLTypeFromCastType($castType)
    {
        switch ($castType) {
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
                \Log::error('Bad cast type: ' . $castType);
                return Type::string();
                break;
        }
    }

    private static $fieldFilterTypes = [];

    private static function getFieldFilterType($castType = 'string')
    {
        $fieldFilterTypeName = 'field_filter_' . $castType;
        if (!isset(self::$fieldFilterTypes[$fieldFilterTypeName])) {
            self::$fieldFilterTypes[$fieldFilterTypeName] = new InputObjectType([
                'name'   => $fieldFilterTypeName,
                'fields' => FilterType::buildFields(self::resolveGraphQLTypeFromCastType($castType))
            ]);
        }
        return self::$fieldFilterTypes[$fieldFilterTypeName];
    }

}