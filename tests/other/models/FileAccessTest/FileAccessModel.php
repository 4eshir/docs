<?php

namespace tests\other\models\FileAccessTest;

class FileAccessModel
{
    //--Тип хранилища файла--
    const SERV = 0;
    const YADI = 1;
    //-----------------------

    public $filepath; // путь к файлу
    public $access; // доступность
    public $repoType = FileAccessModel::SERV; // тип хранилища
}