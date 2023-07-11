<?php

namespace tests\database_rd;

class IntegrityResult
{
    public $tablename; //--имя таблицы
    public $data; //--данные о таблице в формате из основного файла, содержащего сведения о таблицах БД--
    public $result; //--Результат проверки таблицы--

    //--Базовый конструктор--
    function __construct($t_tablename, $t_data, $t_result)
    {
        $this->tablename = $t_tablename;
        $this->data = $t_data;
        $this->result = $t_result;
    }
    //-----------------------
}