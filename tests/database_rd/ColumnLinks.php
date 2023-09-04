<?php

namespace tests\database_rd;

class ColumnLinks
{
    public $columnName;
    public $rows;

    public function __construct($t_columnName, $t_rows)
    {
        $this->columnName = $t_columnName;
        $this->rows = $t_rows;
    }

    //--Проверка массива rows на пустоту--
    public function EmptyCheckRows()
    {
        return count($this->rows) == 0;
    }
    //------------------------------------
}