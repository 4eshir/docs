<?php


namespace app\models\components;


class ArraySqlConstructor
{
    private $table;

    private $query;

    public function table($tablename)
    {
        $this->table = $tablename;
        $this->query = "SELECT * FROM `".$tablename."` ";
    }

    public function joinWith($tablename, $key_dep, $key_main)
    {
        $this->query .= "JOIN `".$tablename."` ON `".$tablename."`.`".$key_dep."` = `".$key_main."` ";
    }

    public function oneWhereAnd($tablename, $column, $array = [])
    {
        $sequence = "";
        foreach ($array as $elem)
            $sequence .= "'".$elem."', ";
        $sequence = substr($sequence, 0, -2);
        if (!stripos($this->query, 'WHERE'))
            $this->query .= "WHERE ";
        else
            $this->query .= "AND ";
        $this->query .= "`".$tablename."`.`".$column."` IN (".$sequence.")";
    }

    public function oneWhereOr($tablename, $column, $array = [])
    {
        $sequence = "";
        foreach ($array as $elem)
            $sequence .= "'".$elem."', ";
        $sequence = substr($sequence, 0, -2);
        if (!stripos($this->query, 'WHERE'))
            $this->query .= "WHERE ";
        else
            $this->query .= "OR ";
        $this->query .= "`".$tablename."`.`".$column."` IN (".$sequence.")";
    }

    public function whereAnd($tablename, $column, $value)
    {
        if (!stripos($this->query, 'WHERE'))
            $this->query .= "WHERE ";
        else
            $this->query .= "AND ";
        $this->query .= "`".$tablename."`.`".$column."` = ".$value." ";
    }


    public function getQuery()
    {
        return $this->query;
    }
}