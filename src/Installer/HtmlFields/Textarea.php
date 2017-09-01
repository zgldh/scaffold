<?php namespace zgldh\Scaffold\Installer\HtmlFields;

use zgldh\Scaffold\Installer\HtmlFields\Search\TextSearch;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
class Textarea extends BaseField
{
    use TextSearch;

    public function html()
    {
        $html = <<<HTML
            <el-form-item label="{$this->getLabel()}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-input v-model="form.{$this->getProperty()}" type="textarea"></el-input>
            </el-form-item>
HTML;

        return $html;
    }
}