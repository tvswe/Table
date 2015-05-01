<?php

namespace Tvswe\Table\Components;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pagination extends TableComponent
{
    /**
     *
     * @var array
     */
    private $perPages;
    
    /**
     *
     * @var integer
     */
    private $perPage;
    
    /**
     *
     * @var integer
     */
    private $pages;
    
    /**
     *
     * @var integer
     */
    private $page;
    
    /**
     *
     * @var integer
     */
    private $count;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->perPages = array(10, 25, 50, 100, 250, 500);
        $this->perPage = 10;
        $this->pages = 1;
        $this->page = 1;
        $this->count = 0;
    }

    /**
     * 
     * @param array $perPages
     */
    public function setPerPages($perPages)
    {
        $this->perPages = $perPages;
    }
    
    /**
     * 
     * @param integer $perPage
     * @param integer $page
     */
    public function setValues($perPage, $page)
    {
        $this->perPage = $perPage;
        $this->page = $page;
    }
    
    /**
     * 
     * @return array
     */
    public function getValue()
    {
        return $this->perPage;
    }

    /**
     * Helper method to reset the Page (called in Table.php)
     */
    public function resetPage()
    {
        $this->page = 1;
    }
    
    /**
     * 
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = $count;
        $this->pages = intval(($this->count - 1) / $this->perPage) + 1;
    }
    
    /**
     * 
     * @return array
     */
    public function getQueryData()
    {
        return array(
            'offset' => $this->getOffset(),
            'length' => $this->perPage
        );
    }
    
    /**
     * 
     * @return array
     */
    public function getData() {
        $offset = $this->getOffset();
        
        return array(
            'perPages' => $this->perPages,
            'perPage' => $this->perPage,
            'pages' => $this->pages,
            'page' => $this->page,
            'firstRow' => $offset + 1,
            'lastRow' => $offset + min($this->perPage, $this->count - $offset),
            'count' => $this->count
        );
    }
    
    /**
     * Calculates the offset
     * @return integer
     */
    private function getOffset()
    {
        return ($this->page - 1) * $this->perPage;
    }
}