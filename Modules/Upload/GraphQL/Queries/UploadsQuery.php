<?php namespace Modules\Upload\GraphQL\Queries;

use App\Scaffold\GraphQL\GraphQL;
use Folklore\GraphQL\Support\Query;
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
        return Type::listOf(GraphQL::getModelObjectType(Upload::class, [
            'user'
        ], 'UploadsQuery'));
    }

    public function args()
    {
        return [
            'filter' => ['name' => 'filter',
                         'type' => GraphQL::getFilterType(Upload::class, [
                             'user'
                         ], 'UploadsQuery')
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Upload::query();
        $result = GraphQL::queryResolver($query, $root, $args);
        return $result;
    }
}