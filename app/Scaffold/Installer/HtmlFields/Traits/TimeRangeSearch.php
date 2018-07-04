<?php namespace App\Scaffold\Installer\HtmlFields\Traits;

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
            <form-item :label="{$languageTerm}">
              <el-time-picker
                is-range
                v-model="searchForm.{$this->getProperty()}"
                column="{$this->getProperty()}"
                operator="range">
              </el-time-picker>
            </form-item>
HTML;

        return $html;
    }
}