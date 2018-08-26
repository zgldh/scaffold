<?php namespace Modules\Setting\Exceptions;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2018/8/26
 * Time: 15:07
 */

use InvalidArgumentException;

class NameDoesNotExist extends InvalidArgumentException
{
    public static function named(string $bundleName, string $roleName)
    {
        return new static("There is no setting named `{$roleName}` in bundle `{$bundleName}`.");
    }
}
