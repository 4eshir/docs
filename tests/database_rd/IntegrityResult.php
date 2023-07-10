<?php

namespace database_rd;

class IntegrityResult
{
    public $data; //--данные о таблице в формате из основного файла, содержащего сведения о таблицах БД--
    public $result; //--Результат проверки таблицы--

    //--Базовый конструктор--
    function __construct($t_data, $t_result)
    {
        $this->data = $t_data;
        $this->result = $t_result;
    }
    //-----------------------
}