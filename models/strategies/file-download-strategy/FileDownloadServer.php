<?php


namespace app\models\strategies\FileDownloadStrategy;

use app\models\strategies\FileDownloadStrategy\AbstractFileDownload;
use yii\db\ActiveRecord;

class FileDownloadServer extends AbstractFileDownload
{
    function __construct($tFilepath, $tFilename)
    {
        $this->filepath = $tFilepath;
        $this->filename = $tFilename;
    }

    public function LoadFile()
    {
        $file = Yii::$app->basePath . '/upload/files/' . $this->filepath . '/' . $this->filename;
        if (file_exists($file)) {
            $this->success = true;
            return \Yii::$app->response->sendFile($file);
        }

        $this->success = false;
    }
}