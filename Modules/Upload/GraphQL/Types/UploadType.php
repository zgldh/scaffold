<?php namespace Modules\Upload\GraphQL\Types;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/30/2018
 * Time: 15:35
 */

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class UploadType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Upload',
        'description' => 'A upload'
    ];

    /*
      * Uncomment following line to make the type input object.
      * http://graphql.org/learn/schema/#input-types
      */
    // protected $inputObject = true;

    public function fields()
    {
        return [
            'id'          => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'The id of the user'
            ],
            'name'        => [
                'type'        => Type::string(),
                'description' => 'The upload name'
            ],
            'description' => [
                'type'        => Type::string(),
                'description' => 'Some description'
            ],
            'disk'        => [
                'type'        => Type::string(),
                'description' => 'filesystem disk name'
            ],
            'path'        => [
                'type'        => Type::string(),
                'description' => 'Relative path to that disk'
            ],
            'size'        => [
                'type'        => Type::string(),
                'description' => 'Upload file size'
            ],
            'type'        => [
                'type'        => Type::string(),
                'description' => 'Upload file type'
            ],
            'user_id'     => [
                'type'        => Type::string(),
                'description' => 'Uploader user ID'
            ],
        ];
    }

    // If you want to resolve the field yourself, you can declare a method
    // with the following format resolve[FIELD_NAME]Field()
    protected function resolveEmailField($root, $args)
    {
        return strtolower($root->email);
    }
}