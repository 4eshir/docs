<?php


namespace app\models\components;



class FileWizard
{
    //--Типы загрузки файлов--
    const SERVER_UPLOAD = 1;
    const YADI_UPLOAD = 2;
    //------------------------

    //--Максимальный размер загрузки файлов на сервер--
    const MAX_SIZE = 26214400;
    //-------------------------------------------------

    static public function CutFilename($filename)
    {
        $result = '';
        $splitName = explode("_", $filename);
        $i = 0;
        while (strlen($result) < 200 - strlen($splitName[$i]) && $i < count($splitName))
        {
            $result = $result."_".$splitName[$i];
            $i++;
        }
        return mb_substr($result, 1);
    }

    static public function UploadFile($localFilepath, $destPath, $fileObj, $uploadType)
    {
        if ($uploadType == FileWizard::SERVER_UPLOAD) $fileObj->saveAs('@app' . $destPath);
        if ($uploadType == FileWizard::YADI_UPLOAD) YandexDiskContext::UploadFileOnDisk($destPath, $localFilepath);
    } 
}