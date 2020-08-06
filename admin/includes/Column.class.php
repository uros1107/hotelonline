<?php
debug_backtrace() || die ("Direct access not permitted");
/**
 * Class of the columns displayed in the listing of a module
 */
class Column
{
    private $name;
    private $label;
    private $type;
    private $value;
    private $table;
    private $fieldRef;
    private $fieldValue;
    private $caseValues;

    public function __construct($name, $label, $type, $table, $fieldRef, $fieldValue, $caseValues)
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->values = array();
        $this->table = $table;
        $this->fieldRef = $fieldRef;
        $this->fieldValue = $fieldValue;
        $this->caseValues = $caseValues;
    }
    function getName()
    {
        return $this->name;
    }
    function getType()
    {
        return $this->type;
    }
    function getLabel()
    {
        return $this->label;
    }
    function getValue($row)
    {
        return htmlentities($this->value[$row], ENT_QUOTES, "UTF-8");
    }
    function getTable()
    {
        return $this->table;
    }
    function getFieldRef()
    {
        return $this->fieldRef;
    }
    function getFieldValue()
    {
        return $this->fieldValue;
    }
    function getCaseValue($case)
    {
        return (isset($this->caseValues[$case])) ? htmlentities($this->caseValues[$case], ENT_QUOTES, "UTF-8") : $case;
    }
    function setValue($row, $value)
    {
        $this->value[$row] = $value;
    }
}
