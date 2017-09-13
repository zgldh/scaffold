<?php namespace zgldh\Scaffold\Installer\HtmlFields;

use zgldh\Scaffold\Installer\HtmlFields\Traits\GetVModel;
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
    use GetVModel;

    public function html()
    {
        $multiple = ':multiple="' . ($this->isMultiple() ? 'true' : 'false') . '"';
        if ($this->isRemote()) {
            $html = <<<HTML
            <el-form-item :label="{$this->getFieldLang(true)}">
              <multiselect {$this->getRelationVmodel('form')} label="{$this->getObjectLabel()}" track-by="id"
                           {$multiple} :placeholder="\$t('scaffold.terms.input_to_search')" 
                           open-direction="bottom" :options="{$this->getComputedPropertyName()}" :searchable="true" 
                           @search-change="{$this->getStoreActionName()}">
              </multiselect>
            </el-form-item>
HTML;
        } else {
            $html = <<<HTML
            <el-form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}" :error="errors.{$this->getProperty()}">
              <el-select {$this->getVmodel('form')} {$multiple} clearable>
                <el-option
                v-for="(item,index) in {$this->getComputedPropertyName()}"
                :key="index"
                :label="item"
                :value="index">
                </el-option>
              </el-select>
            </el-form-item>
HTML;
        }
        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '请选择' . $this->getLabel());
    }

    public function searchHtml()
    {
        if ($this->isRemote()) {
            $remote = $this->getRemoteSelectAttributes();
            $html = <<<HTML
              <el-form-item :label="{$this->getFieldLang(true)}">
                <el-select {$this->getVmodel('searchForm')} clearable {$remote} column="{$this->getProperty()}" operator="=">
                  <el-option
                    v-for="item in {$this->getComputedPropertyName()}"
                    :key="item.id"
                    :label="item.{$this->getObjectLabel()}"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
HTML;
        } else {
            $html = <<<HTML
              <el-form-item :label="{$this->getFieldLang(true)}">
                <el-select {$this->getVmodel('searchForm')} clearable column="{$this->getProperty()}" operator="=">
                  <el-option
                    v-for="(item,index) in {$this->getComputedPropertyName()}"
                    :key="index"
                    :label="item"
                    :value="index">
                  </el-option>
                </el-select>
              </el-form-item>
HTML;
        }
        return $html;
    }

    private function isRemote()
    {
        if ($this->getField() && $this->getField()->getRelationship()) {
            return true;
        }
        return false;
    }

    private function getRemoteSelectAttributes()
    {
        $str = $this->isRemote() ? ('filterable remote :remote-method="' .
            $this->getStoreActionName() .
            '" :placeholder="$t(\'scaffold.terms.input_to_search\')"') : '';
        return $str;
    }

    private function getObjectLabel()
    {
        $relationship = @$this->getField()->getRelationship();
        if ($relationship) {
            $targetModel = $relationship[0];
            $searchColumns = \zgldh\Scaffold\Installer\Utils::getTargetModelSearchColumns($targetModel);
            return $searchColumns[0];
        }
        return 'id';
    }

    private function getRelationVmodel($prefix)
    {
        if ($this->getField()->isRelatingMultiple()) {
            $relatedName = camel_case($this->getProperty());
        } else {
            $relationship = $this->getField()->getRelationship();
            $relatedName = camel_case(basename($relationship[0]));
        }
        return "v-model=\"{$prefix}.{$relatedName}\"";
    }

    private function isMultiple()
    {
        $field = $this->getField();
        return $field->isRelatingMultiple();
    }
}