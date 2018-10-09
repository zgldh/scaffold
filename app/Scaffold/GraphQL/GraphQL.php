<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 10/09/2018
 * Time: 19:25
 */

namespace App\Scaffold\GraphQL;


class GraphQL
{
    private static $types = [];
    private static $schemas = [];

    public static function addSchema($config, $schema = 'default')
    {
        if (isset(self::$schemas[$schema]) && self::$schemas[$schema]) {
            self::$schemas[$schema] = array_merge_recursive(self::$schemas[$schema], $config);
        } else {
            self::$schemas[$schema] = $config;
        }
    }

    public static function addType($typeClass, $typeName)
    {
        self::$types[$typeName] = $typeClass;
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return self::$types;
    }

    /**
     * @return array
     */
    public static function getSchemas(): array
    {
        return self::$schemas;
    }
}