<?php namespace App\Scaffold\Installer\HtmlFields;

use App\Scaffold\Installer\HtmlFields\Traits\TimeRangeSearch;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/31/2017
 * Time: 18:00
 */
class TimePicker extends BaseField
{
    use TimeRangeSearch;

    public function html()
    {
        $html = <<<HTML
            <form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
              <el-time-picker
                v-model="form.{$this->getProperty()}">
              </el-time-picker>
            </form-item>
HTML;

        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '选择时间');
    }

    public function searchType()
    {
        return "Time";
    }
}