<?php

namespace tests\other\models\FileAccessTest;

class TableColumnNames
{
    public $tableName; // заготовка вида Tablename::find()
    public $fileColumns; // названия столбцов с файлами в таблице
    public $pathes; // пути к файлам таблицы (в соответствии с fileColumns

    function __construct($new_tableName, $new_fileColumns, $new_pathes)
    {
        $this->tableName = $new_tableName;
        $this->fileColumns = $new_fileColumns;
        $this->pathes = $new_pathes;
    }
}