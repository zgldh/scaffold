<?php namespace App\Scaffold\Installer\HtmlFields;

use App\Scaffold\Installer\Model\Field;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
abstract class BaseField
{
    /**
     * @var Field
     */
    private $field = null;
    private $options = [];
    private $label = '';
    private $property = '';

    public function __construct($options = [])
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * 得到字段编辑控件HTML
     * @return mixed
     */
    abstract public function html();

    /**
     * 得到字段搜索控件类型
     *
     * @return mixed
     */
    public function searchType(){
        return 'String';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return $this
     */
    public function setProperty($property)
    {
        $this->property = $property;
        return $this;
    }

    public function getStoreState()
    {
        return null;
    }

    public function getComputedPropertyName()
    {
        return $this->getStoreStateName();
    }

    public function getStoreStateName()
    {
        return '_' . camel_case($this->getProperty()) . 'List';
    }

    public function getStoreMutationName()
    {
        return '_' . camel_case('set_' . $this->getProperty()) . 'List';
    }

    public function getStoreActionName()
    {
        return '_' . camel_case('query_' . $this->getProperty()) . 'List';
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Get field language set term
     * @return string
     */
    public function getFieldLang($forFrontEnd = false)
    {
        $term = $this->getField()->getFieldLang($forFrontEnd);
        return $term;
    }

    public function isPropertyTypeInteger()
    {
        if ($this->field) {
            return $this->field->getCastType() === 'integer';
        }
        return false;
    }

    public function isPropertyTypeFloat()
    {
        if ($this->field) {
            return $this->field->getCastType() === 'float';
        }
        return false;
    }

    public function isPropertyTypeBoolean()
    {
        if ($this->field) {
            return $this->field->getCastType() === 'boolean';
        }
        return false;
    }

    public function isPropertyTypeString()
    {
        if ($this->field) {
            return $this->field->getCastType() === 'string';
        }
        return false;
    }
}