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

    //DEBUG
    public $deleteItems = [];
    public $deleteTables = [];
    //DEBUG


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


    //----БЛОК КАСКАДНОГО УДАЛЕНИЯ ЗАПИСЕЙ----

    //--Функция каскадного удаления записей, в которых присутствует запись с $id из таблицы $tablename, а также самой записи $id--
    /*public function CascadeDelete($tablename, $id)
    {
        $this->SetDbArray();
        $selectionIds = $this->GetTableLinks($tablename, $id);

        foreach ($selectionIds as $table)
        {
            $ids = $table->GetAllRowsId();
            foreach ($ids as $id)
                $this->RecursiveDelete($table->tableName, $id);
        }
    }*/
    //----------------------------------------------------------------------------------------------------------------------------

    //--Рекурсивная функция для поиска других записей, связанных с указанной записью и подготовки к удалению--
    /*
     * Результат работы - массив $deleteItems класса RecursiveDeletedItem
     *
     */
    public function RecursiveDelete($tablename, $id)
    {
        $selectionIds = $this->GetTableLinks($tablename, $id);
        foreach ($selectionIds as $table)
        {
            $ids = $table->GetAllRowsId();
            foreach ($ids as $id)
            {
                $item = $this->dbArray[$table->tableName][0]::find()->where(['id' => $id])->one();
                //$item->delete();
                if (!$this->EndDeleteConditional($table->tableName, $id))
                {
                    $this->deleteItems[] = new RecursiveDeletedItem($table->tableName, $item->id, false);
                    $this->RecursiveDelete($table->tableName, $id);
                }
                else
                {
                    $this->deleteItems[] = new RecursiveDeletedItem($table->tableName, $item->id, true);
                    continue;
                }
            }

        }
    }
    //--------------------------------------------------------------------------------------------------------

    //--Функция проверки условия выхода из рекурсивного удаления--
    private function EndDeleteConditional($tablename, $id)
    {
        $selectionIds = $this->GetTableLinks($tablename, $id);

        if (count($selectionIds) == 0) return true; // у таблицы вообще нет связей

        // А если все же есть
        $flag = true;

        foreach ($selectionIds as $table)
            if (count($table->GetAllRowsId()) > 0) // если есть другие записи, связанные с текущей записью
                $flag = false;

        return $flag;
    }
    //------------------------------------------------------------

    //----БЛОК КАСКАДНОГО УДАЛЕНИЯ ЗАПИСЕЙ----






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