<?php


namespace app\models\strategies\FileDownloadStrategy;


use yii\db\ActiveRecord;

abstract class AbstractFileDownload
{
    public $filename;
    public $filepath;

    public $success;
    public $file;

    abstract public function LoadFile();
}