<?php namespace zgldh\Scaffold\Installer\HtmlFields\Traits;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
trait TimeRangeSearch
{
    public function searchHtml()
    {
        $languageTerm = $this->getFieldLang(true);
        $html = <<<HTML
            <el-form-item :label="{$languageTerm}">
              <el-time-picker
                is-range
                v-model="searchForm.{$this->getProperty()}"
                column="{$this->getProperty()}"
                operator="range">
              </el-time-picker>
            </el-form-item>
HTML;

        return $html;
    }
}