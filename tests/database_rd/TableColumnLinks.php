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
            if (!$column->EmptyCheckRows())
                return false;

        return true;
    }
    //-------------------------------------------------------------

    //--Преобразование $columnLinks в array(id1, id2...)--
    public function GetAllRowsId()
    {
        $result = [];
        foreach ($this->columnLinks as $col)
            $result = array_merge($result, $col->rows);

        return $result;
    }
    //----------------------------------------------------
}