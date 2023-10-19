<?php


namespace app\models\strategies\FileDownloadStrategy;

use app\models\components\YandexDiskContext;
use yii\db\ActiveRecord;

class FileDownloadYandexDisk extends AbstractFileDownload
{
    const ADDITIONAL_PATH = 'DSSD'; //дополнительный путь к папке на яндекс диске

    function __construct($tFilepath, $tFilename)
    {
        $this->filepath = $tFilepath;
        $this->filename = $tFilename;
    }
    
    public function LoadFile()
    {
        $res = YandexDiskContext::GetFileFromDisk(self::ADDITIONAL_PATH.$this->filepath, $this->filename);
        $this->file = $res;
        $this->success = true;
    }
}