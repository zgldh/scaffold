<?php namespace Modules\User\GraphQL\Queries;

use App\Scaffold\GraphQL\GraphQL;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;
use Modules\User\Models\User;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/30/2018
 * Time: 15:43
 */
class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'users'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::getModelObjectType(User::class, [
            'avatar'
        ], 'UsersQuery_'));
    }

    public function args()
    {
        return [
            'filter' => ['name' => 'filter',
                         'type' => GraphQL::getFilterType(User::class, [
                             'avatar'
                         ], 'UsersQuery_')
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $query = User::query();
        $result = GraphQL::queryResolver($query, $root, $args);
        return $result;
    }
}