<?php namespace zgldh\Scaffold\Installer\HtmlFields\Search;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
trait DateRangeSearch
{
    public function searchHtml()
    {
        $languageTerm = $this->getFieldLang(true);
        $html = <<<HTML
          <el-form-item :label="{$languageTerm}">
            <el-date-picker
                    v-model="searchForm.{$this->getProperty()}"
                    type="daterange"
                    clearable
                    column="{$this->getProperty()}"
                    operator="range">
            </el-date-picker>
          </el-form-item>
HTML;

        return $html;
    }
}