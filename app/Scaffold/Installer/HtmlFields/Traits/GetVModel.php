<?php namespace App\Scaffold\Installer\HtmlFields\Traits;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
trait GetVModel
{
    private function getVmodel($prefix)
    {
        $str = "v-model=\"{$prefix}.{$this->getProperty()}\"";
//        if ($this->getField() && $this->getField()->getRelationship()) {
//            $str = "v-model.number=\"{$prefix}.{$this->getProperty()}\"";
//        } elseif ($options = $this->getOptions()) {
//            if (count($options) && is_numeric(array_keys($options)[0])) {
//                $str = "v-model.number=\"{$prefix}.{$this->getProperty()}\"";
//            }
//        }
        return $str;
    }
}