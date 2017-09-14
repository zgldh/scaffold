<?php namespace zgldh\Scaffold\Installer\HtmlFields;

use zgldh\Scaffold\Installer\HtmlFields\Traits\TextSearch;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 09/14/2017
 * Time: 18:00
 */
class Uploads extends BaseField
{
    use TextSearch;

    public function html()
    {
        $html = <<<HTML
            <el-form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-input v-model="form.{$this->getProperty()}"></el-input>
              TODO UPLOADS
            </el-form-item>
HTML;

        return $html;
    }
}