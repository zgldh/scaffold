<?php namespace App\Scaffold\Installer\HtmlFields\Traits;

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
          <form-item :label="{$languageTerm}">
            <el-input v-model="searchForm.{$this->getProperty()}" column="{$this->getProperty()}" operator="like"></el-input>
          </form-item>
HTML;

        return $html;
    }
}