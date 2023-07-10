<?php

namespace database_rd;

class DatabaseRD
{
    //--Массив с данными о таблицах БД и связях между ними--
    public $dbArray = [];
    //------------------------------------------------------


    //--Получение данных о таблицах БД из файла $filename--
    public function SetDbArray($filename = 'table_reverse_dependences.php')
    {
        $this->dbArray = include $filename;
    }
    //-----------------------------------------------------


    //--Функция проверки соответствия данных из $dbArray и реальной БД--
    public function CheckDbIntegrity()
    {
        $result = [];
        foreach ($this->dbArray as $table)
        {
            $iterationResult = $this->CheckTableIntegrity($table);
            $integrityResult = new IntegrityResult($table, $iterationResult);
            $result[] = $integrityResult;
        }
        return $result;
    }
    //------------------------------------------------------------------


    //--Функция проверки соответствия одной таблицы из списка таблиц--
    public function CheckTableIntegrity($table)
    {

    }
    //----------------------------------------------------------------
}