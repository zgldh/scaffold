<?php namespace App\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/31/2017
 * Time: 18:00
 */
class DatePicker extends BaseField
{
    public function html()
    {
        $html = <<<HTML
            <form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
              <el-date-picker
                type="date"
                v-model="form.{$this->getProperty()}">
              </el-date-picker>
            </form-item>
HTML;

        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '选择日期');
    }

    public function searchType()
    {
        return "Date";
    }
}