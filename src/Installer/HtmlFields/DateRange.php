<?php namespace zgldh\Scaffold\Installer\HtmlFields;

use zgldh\Scaffold\Installer\HtmlFields\Search\DateRangeSearch;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class DateRange extends BaseField
{
    use DateRangeSearch;

    public function html()
    {
        $html = <<<HTML
            <el-form-item label="{$this->getLabel()}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-date-picker
                type="daterange"
                v-model="form.{$this->getProperty()}"
                placeholder="{$this->getPlaceholder()}">
              </el-date-picker>
            </el-form-item>
HTML;

        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '选择日期范围');
    }
}