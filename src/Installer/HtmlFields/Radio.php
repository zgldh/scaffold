<?php namespace zgldh\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Radio extends BaseField
{
    public function html()
    {
        $options = $this->getOptions() ?: ['1' => 'foo', '2' => 'bar'];
        $html = <<<HTML
<el-form-item label="{$this->getLabel()}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
  <el-radio-group v-model="form.{$this->getProperty()}">
HTML;
        foreach ($options as $value => $label) {
            $html .= <<<HTML
<el-radio label="{$value}">{$label}</el-radio>
HTML;
        }
        $html .= <<<HTML
  </el-radio-group>
</el-form-item>
HTML;
        return $html;
    }

    public function searchHtml()
    {
        $options = $this->getOptions() ?: ['1' => 'foo', '2' => 'bar'];
        $html = <<<HTML
              <el-form-item label="{$this->getLabel()}">
                <el-select v-model="searchForm.{$this->getProperty()}" placeholder="{$this->getPlaceholder()}" clearable
                           column="{$this->getProperty()}" operator="=">
HTML;
        foreach ($options as $value => $label) {
            $html .= <<<HTML
                  <el-option label="{$label}" value="{$value}"></el-option>
HTML;
        }
        $html .= <<<HTML
                </el-select>
              </el-form-item>
HTML;

        return $html;
    }

    private function getPlaceholder()
    {
        return $this->getOption('placeholder', '请选择' . $this->getLabel());
    }
}