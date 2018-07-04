<?php namespace App\Scaffold\Installer;

use Faker\Generator;

class FakerFace extends Generator
{
    private $preCommand = null;

    function __construct($preCommand = '$faker')
    {
        $this->preCommand = $preCommand;
    }

    public function __get($attribute)
    {
        $command = $this->preCommand . '->' . $attribute;
        return $command;
    }

    public function __call($method, $attributes)
    {
        $chainMethods = [
            'optional',
            'unique',
            'valid',
        ];

        $command = $this->preCommand . '->' . $method
            . '(' . join(',', array_map(function ($item) {
                return var_export($item);
            }, $attributes)) . ')';
        if (in_array($method, $chainMethods)) {
            $generator = new FakerFace($command);
            return $generator;
        } else {
            return $command;
        }
    }

    /**
     * @return null|string
     */
    public function getPreCommand()
    {
        return $this->preCommand;
    }
}