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
        $html = <<<HTML
          <el-form-item label="{$this->getLabel()}">
            <el-input v-model="searchForm.{$this->getProperty()}" placeholder="{$this->getLabel()}" column="{$this->getProperty()}" operator="like"></el-input>
          </el-form-item>
HTML;

        return $html;
    }
}