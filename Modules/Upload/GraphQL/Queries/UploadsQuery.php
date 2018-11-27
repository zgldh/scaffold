<?php namespace Modules\Upload\GraphQL\Queries;

use App\Scaffold\GraphQL\GraphMaker;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Upload\Models\Upload;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/30/2018
 * Time: 15:43
 */
class UploadsQuery extends Query
{
    protected $attributes = [
        'name' => 'uploads'
    ];

    public function type()
    {
        return Type::listOf(GraphMaker::getModelObjectType(Upload::class, [
            'user'
        ]));
    }

    public function args()
    {
        return GraphMaker::getQueryArgumentsDefinitionArray(Upload::class, [
            'user']);
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $query = Upload::query();
        $result = GraphMaker::queryResolver($query, $root, $args, $context, $info);
        return $result;
    }
}