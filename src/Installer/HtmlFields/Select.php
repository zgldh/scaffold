<?php namespace zgldh\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Select extends BaseField
{
    public function html()
    {
        $html = <<<HTML
            <el-form-item label="{$this->getLabel()}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-select v-model="form.{$this->getProperty()}" placeholder="{$this->getPlaceholder()}">
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

    public function getComputedCode()
    {
        $options = json_encode($this->getOptions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $js = <<<JS
          {$this->getComputedPropertyName()}: function () {
            return {$options};
          }
JS;
        return $js;
    }
}