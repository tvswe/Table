<?php

namespace Tvswe\Table;

require_once 'FilterModelInterface.php';
require_once 'Components/TableComponent.php';
require_once 'Components/Filter.php';
require_once 'Components/Pagination.php';
require_once 'Components/Sorting.php';

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Table
{
    /**
     * Name
     * @var string
     */
    private $name;
    
    /**
     * Filters
     * @var array
     */
    private $filters;
    
    /**
     * Pagination
     * @var Components\Pagination 
     */
    private $pagination;
    
    /**
     * Sortings
     * @var array
     */
    private $sortings;

    /**
     * Rows
     * @var array
     */
    private $rows;


    /**
     * Constructor
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->filters = array();
        $this->pagination = new Components\Pagination();
        $this->sortings = array();
    }
    
    /**
     * Adds a filter to the table
     * @param \Tvswe\Table\Components\Filter $filter
     */
    public function addFilter(Components\Filter $filter)
    {
        $this->filters[$filter->getName()] = $filter;
    }
    
    /**
     * Adds a sorting to the table
     * @param \Tvswe\Table\Components\Sorting $sorting
     */
    public function addSorting(Components\Sorting $sorting)
    {
        $this->sortings[$sorting->getName()] = $sorting;
    }
    
    public function setPaginationData($perPage, $page)
    {
        $this->pagination->setValues($perPage, $page);
    }
    
    /**
     * Sets the rows of a table
     * $count can be used to set the count of all available rows, otherwise its $rows count
     * @param array $rows
     * @param integer $count
     */
    public function setRows($rows, $count = 0)
    {
        if(!$count) {
            $count = count($rows);
        }
        
        $this->pagination->setCount($count);
        $this->rows = $rows;
    }
    
    /**
     * Returns the name of the table
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the value of the filter with the given name
     * @param string $name
     * @return integer|string
     */
    public function getFilterValue($name)
    {
        return $this->filters[$name]->getValue();
    }
    
    /**
     * Return the data required to select the rows of the table
     * @return array
     */
    public function getQueryData()
    {
        return array(
            'filters' => $this->getFiltersQueryData(),
            'pagination' => $this->pagination->getQueryData(),
            'sortings' => $this->getSortingsQueryData()
        );
    }
    
    /**
     * Helper function to get the required data of all filters to select the rows
     * @return array
     */
    private function getFiltersQueryData()
    {
        $result = array();
        
        foreach($this->filters as $key => $filter) {
            $result[$key] = $filter->getQueryData();
        }
        
        return $result;
    }
    
    /**
     * Helper function to get the required data of all sortings to select the rows
     * @return array
     */
    private function getSortingsQueryData()
    {
        $result = array();
        
        foreach($this->sortings as $key => $sorting) {
            $data = $sorting->getQueryData();
            
            if(!$data) {
                continue;
            }
            
            $result[$data['key']] = array(
                'name' => $key,
                'sort' => $data['sort']
            );
        }
        
        krsort($result);
        
        return $result;
    }
    
    /**
     * Return the data required for the template
     * @return array
     */
    public function getData()
    {
        return array(
            'name' => $this->getName(),
            'filters' => $this->getFiltersData(),
            'pagination' => $this->pagination->getData(),
            'sortings' => $this->getSortingsData(),
            'rows' => $this->rows,
            'hash' => $this->getHash()
        );
    }
    
    /**
     * Helper function to get the required data of all filters for the template
     * @return array
     */
    private function getFiltersData()
    {
        $result = array();
        
        foreach($this->filters as $key => $filter) {
            $result[$key] = $filter->getData();
        }
        
        return $result;
    }
    
    /**
     * Helper function to get the required data of all sortings for the template
     * @return array
     */
    private function getSortingsData()
    {
        $result = array();
        
        foreach($this->sortings as $key => $sorting) {
            $result[$key] = $sorting->getData();
        }
        
        return $result;
    }
    
    /**
     * 
     * @param string $hash
     * @return boolean
     */
    public function checkHash($hash)
    {
        if($hash !== $this->getHash()) {
            $this->pagination->resetPage();
            return false;
        }
        
        return true;
    }

    /**
     * 
     * @return string
     */
    private function getHash() {
        $values = array($this->pagination->getValue());

        /** @var Components\Filter $filter */
        foreach ($this->filters as $filter) {
            $values[] = $filter->getValue();
        }
        
        /** @var Components\Sorting $sorting */
        foreach ($this->sortings as $sorting) {
            $values[] = $sorting->getValue();
        }
        
        $string = implode('|', $values);
        $longHash = md5($string);
        $shortHash = substr($longHash, 0, 6);
        
        return $shortHash;
    }
    
    /**
     * 
     */
    public function initializeSortings()
    {
        $dirtySorting = null;
        $count = count($this->sortings);
        
        /** @var Components\Sorting $sorting */
        foreach($this->sortings as $sorting) {
            if($sorting->isDirty()) {
                $dirtySorting = $sorting;
                continue;
            }
            
            $sorting->initialize($count);
        }
        
        if(!$dirtySorting) {
            return;
        }
        
        if ($dirtySorting->isEnabled()) {
            $this->enableSorting($dirtySorting);
        } elseif ($dirtySorting->isDisabled()) {
            //Deaktiviert
            $this->disableSorting($dirtySorting);
        } elseif ($dirtySorting->isInverted()) {
            //Umgekehrt
            $this->invertSorting($dirtySorting);
        }
    }

    /**
     * 
     * @param Components\Sorting $dirtySorting
     */
    protected function enableSorting(Components\Sorting $dirtySorting)
    {
        $dirtySorting->enable(count($this->sortings));
        
        /** @var Components\Sorting $sorting */
        foreach($this->sortings as $sorting) {            
            if($sorting === $dirtySorting) {
                continue;
            }
            
            $sorting->decrease();
        }
    }
    
    /**
     * 
     * @param Components\Sorting $dirtySorting
     */
    protected function invertSorting(Components\Sorting $dirtySorting)
    {
        $count = count($this->sortings);
        $dirtySorting->initialize($count);
        
        /** @var Components\Sorting $sorting */
        foreach($this->sortings as $sorting) {            
            if($sorting === $dirtySorting) {
                continue;
            }
            
            $sorting->initialize($count);
            
            if($sorting->getBaseValue() > $dirtySorting->getBaseValue()) {
                $sorting->decrease();
            }
        }
        
        $dirtySorting->enable($count);
    }
    
    /**
     * 
     * @param Components\Sorting $dirtySorting
     * @param integer $previousValue
     */
    protected function disableSorting(Components\Sorting $dirtySorting)
    {
        $previousBaseValue = $dirtySorting->getPreviousBaseValue();
        $count = count($this->sortings);
        $dirtySorting->initialize($count);
        
        /** @var Components\Sorting $sorting */
        foreach($this->sortings as $sorting) {  
            if($sorting === $dirtySorting) {
                continue;
            }
            
            $sorting->initialize($count);
            
            if($sorting->getBaseValue() < $previousBaseValue) {
                $sorting->increase();
            }
        }
    }
}
