<?php namespace zgldh\Scaffold\Installer\HtmlFields;

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
            <el-form-item label="{$this->getLabel()}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-input v-model="form.{$this->getProperty()}" type="password"></el-input>
            </el-form-item>
HTML;

        return $html;
    }

    public function searchHtml()
    {
        return null;
    }
}