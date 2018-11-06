<?php namespace Modules\User\GraphQL\Queries;

use App\Scaffold\GraphQL\GraphMaker;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
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
        return Type::listOf(GraphMaker::getModelObjectType(User::class, [
            'avatar'
        ]));
    }

    public function args()
    {
        return [
            'filter' => ['name' => 'filter',
                         'type' => GraphMaker::getFilterType(User::class, [
                             'avatar'
                         ])
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $query = User::query();
        $result = GraphMaker::queryResolver($query, $root, $args, $context, $info);
        return $result;
    }
}