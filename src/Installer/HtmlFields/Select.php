<?php namespace zgldh\Scaffold\Installer\HtmlFields;

use zgldh\Scaffold\Installer\HtmlFields\Traits\StoreState;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Select extends BaseField
{
    use StoreState;

    public function html()
    {
        $remote = $this->getRemoteSelectAttributes();
        $html = <<<HTML
            <el-form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-select v-model="form.{$this->getProperty()}" clearable {$remote}>
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
        $remote = $this->getRemoteSelectAttributes();
        $html = <<<HTML
              <el-form-item :label="{$this->getFieldLang(true)}">
                <el-select v-model="searchForm.{$this->getProperty()}" clearable {$remote} column="{$this->getProperty()}" operator="=">
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

    private function getRemoteSelectAttributes()
    {
        $str = '';
        if ($this->getField() && $this->getField()->getRelationship()) {
            $str = 'filterable remote :remote-method="' . $this->getStoreActionName() . '" :placeholder="$t(\'scaffold.terms.input_to_search\')"';
        }
        return $str;
    }
}