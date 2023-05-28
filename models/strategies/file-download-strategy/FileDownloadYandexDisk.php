<?php


namespace app\models\strategies\FileDownloadStrategy;

use app\models\strategies\FileDownloadStrategy\AbstractFileDownload;
use yii\db\ActiveRecord;

class FileDownloadYandexDisk extends AbstractFileDownload
{
    function __construct($tFilepath, $tFilename)
    {
        $this->filepath = $tFilepath;
        $this->filename = $tFilename;
    }
    
    public function LoadFile()
    {
        
    }
}