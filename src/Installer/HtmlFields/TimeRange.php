<?php namespace zgldh\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/31/2017
 * Time: 18:00
 */
class TimeRange extends BaseField
{
    public function html()
    {
        $html = <<<HTML
            <el-form-item label="{$this->getLabel()}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-time-picker
                is-range
                v-model="form.{$this->getProperty()}"
                placeholder="{$this->getPlaceholder()}">
              </el-time-picker>
            </el-form-item>
HTML;

        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '选择时间范围');
    }
}