<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 10/09/2018
 * Time: 19:25
 */

namespace App\Scaffold\GraphQL;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

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
        return [
            'eq'         => [
                'type'        => Type::string(),
                'description' => 'The field value equals the provided argument'
            ],
            'neq'        => [
                'type'        => Type::string(),
                'description' => 'The field value does not equal the provided argument'
            ],
            'gt'         => [
                'type'        => Type::string(),
                'description' => 'The field value is greater than the provided argument'
            ],
            'lt'         => [
                'type'        => Type::string(),
                'description' => 'The field value is less than the provided argument'
            ],
            'egt'        => [
                'type'        => Type::string(),
                'description' => 'The field value is equals and greater than the provided argument'
            ],
            'elt'        => [
                'type'        => Type::string(),
                'description' => 'The field value is equals and less than the provided argument'
            ],
            'in'         => [
                'type'        => Type::listOf(Type::string()),
                'description' => 'The field value can be found in the provided argument list'
            ],
            'notIn'      => [
                'type'        => Type::listOf(Type::string()),
                'description' => 'The field value can NOT be found in the provided argument list'
            ],
            'between'    => [
                'type'        => Type::listOf(Type::string()),
                'description' => 'The field value is between the provided two arguments. Borders excluded.'
            ],
            'notBetween' => [
                'type'        => Type::listOf(Type::string()),
                'description' => 'The field value is NOT between the provided two arguments. Borders excluded.'
            ],
            'like'       => [
                'type'        => Type::string(),
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
}