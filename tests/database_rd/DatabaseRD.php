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

    
}