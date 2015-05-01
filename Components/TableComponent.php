<?php

namespace Tvswe\Table\Components;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class TableComponent
{
    /**
     * 
     */
    abstract public function getQueryData();
    
    /**
     * 
     * @return array
     */
    public function getData()
    {
        return $this->getQueryData();
    }
}