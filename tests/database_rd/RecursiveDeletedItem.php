<?php

namespace tests\database_rd;

class RecursiveDeletedItem
{
    public $tableName; // имя таблицы
    public $id; // идентификатор удаляемой записи
    public $endSign; // признак конца рекурсивного спуска

    public function __construct($t_tableName, $t_id, $t_endSign)
    {
        $this->tableName = $t_tableName;
        $this->id = $t_id;
        $this->endSign = $t_endSign;
    }
}