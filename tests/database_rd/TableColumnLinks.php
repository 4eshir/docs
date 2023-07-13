<?php

namespace tests\database_rd;

class TableColumnLinks
{
    public $tableName;
    public $columnLinks;

    public function __construct($t_tableName, $t_columnLinks)
    {
        $this->tableName = $t_tableName;
        $this->columnLinks = $t_columnLinks;
    }

    //--Проверка массивов rows из массива $columnLinks на пустоту--
    public function EmptyCheckColumnLinks()
    {
        foreach ($this->columnLinks as $column)
            if ($column->EmptyCheckRows())
                return true;

        return false;
    }
    //-------------------------------------------------------------
}