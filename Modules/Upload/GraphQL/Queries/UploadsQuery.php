<?php namespace Modules\Upload\GraphQL\Queries;

use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;
use Modules\Upload\Models\Upload;
use Modules\User\Models\User;

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
        return Type::listOf(\GraphQL::type('Upload'));
    }

    public function args()
    {
        return [
            'id'   => ['name' => 'id', 'type' => Type::string()],
            'name' => ['name' => 'name', 'type' => Type::string()],
            'disk' => ['name' => 'disk', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args)
    {
        if (isset($args['id'])) {
            return Upload::where('id', $args['id'])->get();
        } else if (isset($args['name'])) {
            return Upload::where('name', $args['name'])->get();
        } else if (isset($args['disk'])) {
            return Upload::where('disk', $args['disk'])->get();
        } else {
            return Upload::all();
        }
    }
}