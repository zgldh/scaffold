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

// TODO
class QueryOptions extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Query Options',
        'description' => 'Pagination, sorting, and more. '
    ];

    /*
      * Uncomment following line to make the type input object.
      * http://graphql.org/learn/schema/#input-types
      */
    protected $inputObject = true;

    public function fields()
    {
        return [
            'filter_relations' => [
                'type'        => Type::boolean(),
                'description' => 'Whether the filter will affect relationships retrieving or not.'
            ],
            'page_size'        => [
                'description' => "The max amount of items in a page. Default is 100.",
                'type'        => Type::int()],
            'page_num'         => [
                'description' => "The page which you would like to retrieve. Default is 1.",
                'type'        => Type::int()
            ],
            'sort_field'       => [
                'type'        => Type::string(),
                'description' => 'The field you would like to sort by. String like "created_at", or "user.name". '
            ],
            'sort_direction'   => [
                'type'        => Type::string(),
                'description' => 'The sorting direction. "ASC" or "DESC".'
            ]
        ];
    }
}