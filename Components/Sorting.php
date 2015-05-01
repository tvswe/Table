<?php

namespace Tvswe\Table\Components;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Sorting extends TableComponent
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var integer 
     */
    private $value;
    
    /**
     *
     * @var integer 
     */
    private $baseValue;
    
    /**
     *
     * @var integer
     */
    private $previous;
    
    /**
     * 
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->value = 0;
        $this->baseValue = 0;
        $this->previous = 0;
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @param integer $value
     * @param integer $previous
     */
    public function setValue($value, $previous)
    {
        $this->value = intval($value);
        $this->previous = intval($previous);
    }
    
    /**
     * Returns the current value
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Returns the current base value
     * @return integer
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }
    
    /**
     * 
     * @return bool
     */
    public function isDirty()
    {
        return ($this->value !== $this->previous);
    }
    
    /**
     * 
     * @return bool
     */
    public function isInverted()
    {
        return ($this->value = -$this->previous);
    }
    
    /**
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return (($this->value !== 0) && ($this->previous === 0));
    }
    
    /**
     * 
     * @return bool
     */
    public function isDisabled()
    {
        return (($this->value === 0) && ($this->previous !== 0));
    }
    
    /**
     * 
     * @return integer
     */
    public function getPreviousBaseValue()
    {
        return abs($this->previous);
    }

    /**
     * Initializes the sorting
     * @param integer $count
     */
    public function initialize($count = 1)
    {
        $this->baseValue = ($this->value === 0) ? intval($count) : abs($this->value);
    }
    
    /**
     * Enables a disabled sorting
     * @param type $count
     */
    public function enable($count = 1)
    {
        $this->baseValue = intval($count);
        $this->value = ($this->value < 0) ? -($this->baseValue) : $this->baseValue;
    }
    
    /**
     * Increases the base value when a other sorting was disabled
     */
    public function increase()
    {
        if ($this->value !== 0) {
            $this->baseValue = abs($this->value) + 1;
            $this->value = ($this->value < 0) ? -($this->baseValue) : $this->baseValue;
        }
    }
    
    /**
     * Decreased the base value when a other sorting was inverted (removed to top of the order)
     */
    public function decrease()
    {
        if ($this->value !== 0) {
            $this->baseValue = abs($this->value) - 1;
            $this->value = ($this->value < 0) ? -($this->baseValue) : $this->baseValue;
        }
    }
    
    /**
     * 
     * @return type
     */
    public function getQueryData()
    {
        if ($this->value === 0) {
            return null;
        }
        
        return array(
            'key' => $this->baseValue,
            'sort' => ($this->value > 0) ? 'ASC' : 'DESC',
        );
    }
    
    /**
     * 
     * @return type
     */
    public function getData() {
        return array(
            'name' => $this->name,
            'value' => $this->value,
            'baseValue' => $this->baseValue
        );
    }
}