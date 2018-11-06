<?php namespace Modules\Post\GraphQL\Queries;

use App\Scaffold\GraphQL\GraphMaker;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;
use Modules\User\Models\User;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/30/2018
 * Time: 15:43
 */
class PostsQuery extends Query
{
    protected $attributes = [
        'name' => 'users'
    ];

    public function type()
    {
        return Type::listOf(GraphMaker::getModelObjectType(User::class, [
            'avatar'
        ], 'UsersQuery_'));
    }

    public function args()
    {
        return [
            'filter' => ['name' => 'filter',
                         'type' => GraphMaker::getFilterType(User::class, [
                             'avatar'
                         ], 'UsersQuery_')
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $query = User::query();
        $result = GraphMaker::queryResolver($query, $root, $args);
        return $result;
    }
}