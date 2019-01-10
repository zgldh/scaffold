<?php namespace Modules\User\GraphQL\Queries;

use App\Scaffold\GraphQL\GraphMaker;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\User\Models\User;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 01/10/2019
 * Time: 15:43
 */
class UserQuery extends Query
{
    protected $attributes = [
        'name' => 'user'
    ];

    public function type()
    {
        return GraphMaker::getModelObjectType(User::class, [
            'avatar',
            'uploads'
        ]);
    }

    public function args()
    {
        return GraphMaker::getQueryArgumentsForIndividual(User::class, [
            'avatar',
            'uploads']);
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $query = User::query();
        $result = GraphMaker::individualQueryResolver($query, $root, $args, $context, $info);
        return $result;
    }
}