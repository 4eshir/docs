<?php

namespace tests\database_rd;

use app\models\common\People;
use app\models\work\PeopleWork;
use tests\database_rd\ColumnLinks;
use tests\database_rd\TableColumnLinks;

class DatabaseRD
{
    //--Массив с данными о таблицах БД и связях между ними--
    public $dbArray = [];
    //------------------------------------------------------

    public $filename = '';


    //--Получение данных о таблицах БД из файла $filename--
    public function SetDbArray($filename = 'table_reverse_dependences.php')
    {
        $this->dbArray = include $filename;
        $this->filename = $filename;
    }
    //-----------------------------------------------------

    //--Получение записей, которые связаны с текущей записью в таблице--
    /*
     * $tablename - имя таблицы
     * $id - идентификатор записи в таблице
     *
     * return array("tablename" => class TableColumnLinks(), ...)
    */
    public function GetTableLinks($tablename, $id)
    {
        $mainTable = $this->dbArray[$tablename];

        $result = array();
        foreach ($mainTable as $key => $dTable)
        {

            if (gettype($dTable) == 'array')
            {
                $rdTable = $this->dbArray[$key][0];

                $colLinks = array();
                foreach ($dTable as $col)
                {
                    $tempIds = array();
                    $query = $rdTable::find()->where([$col => $id])->all();
                    foreach ($query as $row)
                        $tempIds[] = $row->id;

                    $colLinks[] = new ColumnLinks($col, $tempIds);
                }

                $result[] = new TableColumnLinks($key, $colLinks);
            }

        }

        return $result;
    }
    //------------------------------------------------------------------


    //--Функция проверки соответствия данных из $dbArray и реальной БД--
    public function CheckDbIntegrity()
    {
        $result = [];
        foreach ($this->dbArray as $key => $table)
        {
            $iterationResult = $this->CheckTableIntegrity($table); // проверка одной таблицы
            $integrityResult = new IntegrityResult($key, $table, $iterationResult);
            $result[] = $integrityResult;
        }
        return $result;
    }
    //------------------------------------------------------------------

    //--Функция каскадного удаления записей, в которых присутствует запись с $id из таблицы $tablename, а также самой записи $id--
    public function CascadeDelete($tablename, $id)
    {
        $selectionIds = $this->GetTableLinks($tablename, $id);

        foreach ($selectionIds as $table)
        {

        }
    }
    //----------------------------------------------------------------------------------------------------------------------------






    //--Функция проверки соответствия одной таблицы из списка таблиц--
    private function CheckTableIntegrity($table)
    {
        $integrityFlag = true; // флаг ошибки целостности
        $integrityErrors = []; // список столбцов с нарушениями целостности
        $dependTable = null;

        // 0-ой элемент - заготовка класса Table
        foreach ($table as $key => $value)
        {

            if (gettype($value) == 'array')
            {
                $dependTable = $this->dbArray[$key][0];
                $tempCols = []; // все столбцы с ошибками из таблицы $value
                foreach ($value as $column)
                {
                    $query = null;

                    try
                    {
                        $query = $dependTable::find()->where([$column => 1])->one();
                    }
                    catch (\yii\db\Exception $e)
                    {
                        $integrityFlag = false;
                        $tempCols[] = $column;
                    }

                }

                if (count($tempCols) > 0)
                    $integrityErrors += [$key => $tempCols];
            }
        }

        return [$integrityFlag, $integrityErrors];
    }
    //----------------------------------------------------------------



}