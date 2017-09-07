<?php namespace zgldh\Scaffold\Installer\HtmlFields;

use zgldh\Scaffold\Installer\HtmlFields\Traits\ComputedCode;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Select extends BaseField
{
    use ComputedCode;

    public function html()
    {
        $html = <<<HTML
            <el-form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-select v-model="form.{$this->getProperty()}">
                <el-option
                v-for="(item,index) in {$this->getComputedPropertyName()}"
                :key="index"
                :label="item"
                :value="index">
                </el-option>
              </el-select>
            </el-form-item>
HTML;

        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '请选择' . $this->getLabel());
    }

    public function searchHtml()
    {
        $html = <<<HTML
              <el-form-item :label="{$this->getFieldLang(true)}">
                <el-select v-model="searchForm.{$this->getProperty()}" clearable column="{$this->getProperty()}" operator="=">
                  <el-option
                    v-for="(item,index) in {$this->getComputedPropertyName()}"
                    :key="index"
                    :label="item"
                    :value="index">
                  </el-option>
                </el-select>
              </el-form-item>
HTML;

        return $html;
    }
}