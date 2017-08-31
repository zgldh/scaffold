<?php namespace zgldh\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/30/2017
 * Time: 18:00
 */
abstract class BaseField
{
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

    abstract public function html();

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

    public function getComputedCode()
    {
        return null;
    }

    public function getComputedPropertyName()
    {
        return '_' . $this->getProperty().'_list';
    }
}