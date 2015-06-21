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
    private function __construct($name, array $options)
    {
        $this->name = $name;
        $this->value = 0;
        $this->options = $options;
    }

    /**
     * 
     * @param string $name
     * @param array $options
     */
    public static function fromArray($name, array $options)
    {
	$filter = new Filter($name, $options);

	return $filter;
    }

    /**
     * 
     * @param string $name
     * @param FilterModelInterface $model
     * @param array $arguments
     */
    public static function fromModel($name, FilterModelInterface $model, array $arguments = [])
    {
	$options = $model->getFilterOptions($arguments);
	$filter = self::fromArray($name, $options);

	return $filter;
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
