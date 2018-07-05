<?php namespace App\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class DateRange extends BaseField
{
    public function html()
    {
        $html = <<<HTML
            <form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
              <el-date-picker
                type="daterange"
                v-model="form.{$this->getProperty()}">
              </el-date-picker>
            </form-item>
HTML;

        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '选择日期范围');
    }

    public function searchType()
    {
        return "Date,
            ComponentParameters: {
              Type: daterange
            }";
    }
}