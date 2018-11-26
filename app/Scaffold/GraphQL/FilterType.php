<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 10/09/2018
 * Time: 19:25
 */

namespace App\Scaffold\GraphQL;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;

class FilterType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Filter',
        'description' => 'The field filter'
    ];

    /*
      * Uncomment following line to make the type input object.
      * http://graphql.org/learn/schema/#input-types
      */
    protected $inputObject = true;

    public function fields()
    {
        return self::buildFields(Type::string());
    }

    /**
     * @param ObjectType|InterfaceType|UnionType|ScalarType|InputObjectType|EnumType|ListOfType|NonNull $type Type::string()|Type::int()|Type::float()|Type::boolean();
     * @return array
     */
    public static function buildFields($type)
    {
        return [
            'eq'         => [
                'type'        => $type,
                'description' => 'The field value equals the provided argument'
            ],
            'neq'        => [
                'type'        => $type,
                'description' => 'The field value does not equal the provided argument'
            ],
            'gt'         => [
                'type'        => $type,
                'description' => 'The field value is greater than the provided argument'
            ],
            'lt'         => [
                'type'        => $type,
                'description' => 'The field value is less than the provided argument'
            ],
            'egt'        => [
                'type'        => $type,
                'description' => 'The field value is equals and greater than the provided argument'
            ],
            'elt'        => [
                'type'        => $type,
                'description' => 'The field value is equals and less than the provided argument'
            ],
            'in'         => [
                'type'        => Type::listOf($type),
                'description' => 'The field value can be found in the provided argument list'
            ],
            'notIn'      => [
                'type'        => Type::listOf($type),
                'description' => 'The field value can NOT be found in the provided argument list'
            ],
            'between'    => [
                'type'        => Type::listOf($type),
                'description' => 'The field value is between the provided two arguments. Borders excluded.'
            ],
            'notBetween' => [
                'type'        => Type::listOf($type),
                'description' => 'The field value is NOT between the provided two arguments. Borders excluded.'
            ],
            'like'       => [
                'type'        => $type,
                'description' => 'The field value is like the provided argument.'
            ],
            'null'       => [
                'type'        => Type::boolean(),
                'description' => 'The field value is null.'
            ],
            'notNull'    => [
                'type'        => Type::boolean(),
                'description' => 'The field value is NOT null.'
            ]
        ];
    }

    public static $filterOperators = [
        'eq',
        'neq',
        'gt',
        'lt',
        'egt',
        'elt',
        'in',
        'notIn',
        'between',
        'notBetween',
        'like',
        'null',
        'notNull',
    ];

    public static function isFilterOperators($items)
    {
        if (is_string($items)) {
            return in_array($items, self::$filterOperators);
        }
        if (is_array($items)) {
            $allOperators = true;
            foreach ($items as $item) {
                if (!self::isFilterOperators($item)) {
                    $allOperators = false;
                    break;
                }
            }
            return $allOperators;
        }
        return false;
    }
}