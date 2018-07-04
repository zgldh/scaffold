<?php namespace App\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
class Password extends BaseField
{
    public function html()
    {
        $html = <<<HTML
            <form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
              <el-input v-model="form.{$this->getProperty()}" type="password"></el-input>
            </form-item>
HTML;

        return $html;
    }
}