<?php
namespace Tvswe\Table;

use Tvswe\Table\Components\Filter;
use Tvswe\Table\Components\Sorting;

trait TableTrait {
    /**
     * Tables
     * @var Table[]
     */
    private $tables = [];
    
    /**
     * Table data source
     * @var array
     */
    private $tableData = [];
    
    /**
     * Sets the data source for all tables (e.g. $_POST)
     * @param array $tableData
     */
    protected function setTableData(array $tableData)
    {
        $this->tableData = $tableData;
    }


    /**
     * Creates and returns a table.
     * The pagination data will be set, if table data exists.
     * @param string $name
     * @return Table
     */
    protected function createTable($name)
    {
        $table = new Table($name);
        
        if (array_key_exists($name, $this->tableData)) {
            $tableData = $this->tableData[$name];
            $perPage = $tableData['perPage'];
            $page = isset($tableData['fastPage']) ? $tableData['fastPage'] : $tableData['page'];
        
            $table->setPaginationData($perPage, $page);
        }
        
        return $table;
    }

    /**
     * Adds a table to the tables array
     * @param Table $table
     */
    protected function addTable(Table $table)
    {
        $this->tables[$table->getName()] = $table;
    }
    
   /**
    * Adds a filter to a table
    * @param Table $table
    * @param Filter $filter
    */
   protected function addTableFilter(Table $table, Filter $filter)
    {
        $value = $this->getTableComponentValue(
                    $table->getName(),
                    $filter,
                    0
                );
       
        $filter->setValue($value);
        $table->addFilter($filter);
    }
    
    /**
     * Adds a sorting to a table
     * @param Table $table
     * @param Sorting $sorting
     */
    protected function addTableSorting(Table $table, Sorting $sorting)
    {
        $values = $this->getTableComponentValue(
                    $table->getName(),
                    $sorting,
                    array(
                        'value' => 0,
                        'previous' => 0
                    )
                );
        
        $sorting->setValue($values['value'], $values['previous']);
        $table->addSorting($sorting);
    }
    
    /**
     * Helper method to get the component data from table data
     * @param string $tableName
     * @param Filter|Sorting $component
     * @param integer|string|null $fallback
     */
    private function getTableComponentValue($tableName, \Tvswe\Table\Components\TableComponent $component, $fallback = null)
    {
        if ($component instanceof Filter) {
            $type = 'filters';
        } elseif ($component instanceof Sorting) {
            $type = 'sortings';
        }
        
        if(isset($this->tableData[$tableName][$type][$component->getName()])) {
            return $this->tableData[$tableName][$type][$component->getName()];
        } else {
            return $fallback;
        }
    }
    
    /**
     * Returns the query data
     * @param Table $table
     * @return array
     */
    protected function getTableQueryData(Table $table)
    {
        $tableName = $table->getName();
        $tablePost = $this->tableData;
        
        if (isset($tablePost[$tableName]['hash'])) {
            $table->checkHash($tablePost[$tableName]['hash']);
        }
        
        return $table->getQueryData();
    }
    
    /**
     * Loads the data of all tables
     * @return array
     */
    protected function loadTables()
    {
        $tables = array();
        
        /** @var Table $table */
        foreach($this->tables as $name => $table) {
            $tables[$name] = $table->getData();
        }
        
        return $tables;
    }
    
    /**
     * Returns a table
     * @param string $name
     * @return Table
     */
    protected function getTable($name)
    {
        return $this->tables[$name];
    }
}