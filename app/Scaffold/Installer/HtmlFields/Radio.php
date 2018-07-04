<?php namespace App\Scaffold\Installer\HtmlFields;

use App\Scaffold\Installer\HtmlFields\Traits\GetVModel;
use App\Scaffold\Installer\HtmlFields\Traits\StoreState;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Radio extends BaseField
{
    use StoreState;
    use GetVModel;

    public function html()
    {
        $options = $this->getOptions() ?: ['1' => 'foo', '2' => 'bar'];
        $html = <<<HTML
<form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
  <el-radio-group {$this->getVmodel('form')}>
HTML;
        foreach ($options as $value => $label) {
            if ($this->isPropertyTypeBoolean() || $this->isPropertyTypeFloat() || $this->isPropertyTypeInteger()) {
                $labelAttribute = ':label="' . $value . '"';
            } else {
                $labelAttribute = 'label="' . $value . '"';
            }
            $html .= <<<HTML
<el-radio {$labelAttribute}>{$label}</el-radio>
HTML;
        }
        $html .= <<<HTML
  </el-radio-group>
</form-item>
HTML;
        return $html;
    }


    public function searchType()
    {
        return 'Select';
    }
}