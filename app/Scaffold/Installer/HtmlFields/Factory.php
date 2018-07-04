<?php namespace App\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Factory
{
    /**
     * @param $fieldSource
     * @return BaseField
     * @throws \Exception
     */
    public static function getField($fieldSource)
    {
        if (is_a($fieldSource, BaseField::class)) {
            return $fieldSource;
        }

        $fieldClassName = __NAMESPACE__ . '\\' . ucfirst(camel_case($fieldSource));
        if (!class_exists($fieldClassName)) {
            throw new \Exception("Field class doesn't exist {$fieldClassName}");
        }

        return new $fieldClassName;
    }
}