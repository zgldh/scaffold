<?php namespace Modules\Upload\GraphQL\Queries;

use Folklore\GraphQL\Support\InterfaceType;
use GraphQL\Type\Definition\Type;
use Modules\Upload\Models\Upload;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/30/2018
 * Time: 15:43
 */
class UploadableInterface extends InterfaceType
{
    protected $attributes = [
        'name'        => 'Uploadable',
        'description' => 'uploads table records related object'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The id of the uploadable object.'
            ]
        ];
    }

    public function resolveType($root, $args)
    {
        // TODO need a interface resolver
        // Use the resolveType to resolve the Type which is implemented trough this interface
        $type = $root['type'];
        if ($type === 'human') {
            return \GraphQL::type('Human');
        } else if ($type === 'droid') {
            return \GraphQL::type('Droid');
        }
    }
}