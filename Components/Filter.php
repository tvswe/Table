<?php

namespace Tvswe\Table\Components;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Filter extends TableComponent
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var integer|string 
     */
    private $value;
    
    /**
     *
     * @var array
     */
    private $options;
    
    /**
     * 
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->value = 0;
        $this->options = array();
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
     * @param integer|string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * 
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
    
    /**
     * 
     * @return integer|string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * 
     * @return integer|string
     */
    public function getQueryData()
    {
        return $this->value;
    }
    
    /**
     * 
     * @return array
     */
    public function getData() {
        return array(
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options
        );
    }
}