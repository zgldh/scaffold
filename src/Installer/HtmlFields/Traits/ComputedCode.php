<?php namespace zgldh\Scaffold\Installer\HtmlFields\Traits;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 09/07/2017
 * Time: 18:00
 */
trait ComputedCode
{
    public function getComputedCode()
    {
        if ($this->getField() && $this->getField()->getRelationship()) {
            $code = $this->getAjaxForRelactionship();
        } elseif ($this->getOptions()) {
            $code = $this->getHardCodedList();
        } else {
            $code = "return []";
        }
        $js = <<<JS
          {$this->getComputedPropertyName()}: function () {
            {$code};
          }
JS;
        return $js;
    }

    private function getHardCodedList()
    {
        $options = json_encode($this->getOptions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return "return " . $options;
    }

    private function getAjaxForRelactionship()
    {
        $options = json_encode($this->getOptions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return "return " . $options;
    }
}