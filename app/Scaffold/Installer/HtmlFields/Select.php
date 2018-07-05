<?php namespace App\Scaffold\Installer\HtmlFields;

use App\Scaffold\Installer\HtmlFields\Traits\GetVModel;
use App\Scaffold\Installer\HtmlFields\Traits\StoreState;

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
            <form-item :label="{$this->getFieldLang(true)}">
              <multiselect {$this->getRelationVmodel('form')} label="{$this->getObjectLabel()}" track-by="id"
                           {$multiple} :placeholder="\$t('scaffold.terms.input_to_search')" 
                           open-direction="bottom" :options="{$this->getComputedPropertyName()}" :searchable="true" 
                           @search-change="{$this->getStoreActionName()}">
              </multiselect>
            </form-item>
HTML;
        } else {
            $html = <<<HTML
            <form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
              <el-select {$this->getVmodel('form')} {$multiple} clearable>
                <el-option
                v-for="(item,index) in {$this->getComputedPropertyName()}"
                :key="index"
                :label="item"
                :value="index">
                </el-option>
              </el-select>
            </form-item>
HTML;
        }
        return $html;
    }

    public function getPlaceholder()
    {
        return $this->getOption('placeholder', '请选择' . $this->getLabel());
    }

    private function isRemote()
    {
        if ($this->getField() && $this->getField()->getRelationship()) {
            return true;
        }
        return false;
    }

    private function getObjectLabel()
    {
        $relationship = @$this->getField()->getRelationship();
        if ($relationship) {
            $targetModel = $relationship[0];
            $searchColumns = \App\Scaffold\Installer\Utils::getTargetModelSearchColumns($targetModel);
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

    public function searchType()
    {
        return 'Select';
    }
}