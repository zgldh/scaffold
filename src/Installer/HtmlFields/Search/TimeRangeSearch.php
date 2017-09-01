<?php namespace zgldh\Scaffold\Installer\HtmlFields\Search;

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
        $html = <<<HTML
            <el-form-item label="{$this->getLabel()}">
              <el-time-picker
                is-range
                v-model="searchForm.{$this->getProperty()}"
                placeholder="{$this->getPlaceholder()}"
                column="{$this->getProperty()}"
                operator="range">
              </el-time-picker>
            </el-form-item>
HTML;

        return $html;
    }
}