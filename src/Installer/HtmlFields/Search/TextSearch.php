<?php namespace zgldh\Scaffold\Installer\HtmlFields\Search;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
trait TextSearch
{
    public function searchHtml()
    {
        $languageTerm = $this->getFieldLang(true);
        $html = <<<HTML
          <el-form-item :label="{$languageTerm}">
            <el-input v-model="searchForm.{$this->getProperty()}" column="{$this->getProperty()}" operator="like"></el-input>
          </el-form-item>
HTML;

        return $html;
    }
}