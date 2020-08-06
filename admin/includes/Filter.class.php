<?php
debug_backtrace() || die ("Direct access not permitted");
/**
 * Class of the filters displayed in the search engine in the listing of a module
 */
class Filter
{
    private $name;
    private $label;
    private $value;
    private $options;
    private $order;
    private $filterName;
    private $optFilters;

    public function __construct ($name, $label, $options, $order, $filterName, $optFilters)
    {
        $this->name = $name;
        $this->label = $label;
        if(is_array($options))
            $this->options = $options;
        $this->filterName = $filterName;
        $this->optFilters = $optFilters;
    }
    function getName()
    {
        return $this->name;
    }
    function getLabel()
    {
        return $this->label;
    }
    function getValue()
    {
        return $this->value;
    }
    function getOrder()
    {
        return $this->order;
    }
    function getOptions()
    {
        return $this->options;
    }
    function getOptFilters()
    {
        return $this->optFilters;
    }
    function getFilterName()
    {
        return $this->filterName;
    }
    function setValue($value)
    {
        $this->value = $value;
    }
}
