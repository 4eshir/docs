<?php

namespace app\components\traits;

use yii\web\UploadedFile;

trait FileWizardTrait
{
    /**
     * Загрузка файла на сервер
     * @param UploadedFile $file
     * @param string $filepath Путь для сохранения файла
     * @return void
     */
    public function saveFile(UploadedFile $file, $filepath)
    {
        $file->saveAs($filepath);
    }

    /**
     * Обрезка имени файла
     * @param string $filename Имя файла
     * @param int $nameLen Максимальная длина имени файла
     * @return string
     */
    public function cutFilename($filename, $nameLen = 200)
    {
        $result = '';
        $splitName = explode("_", $filename);
        $i = 0;
        while (strlen($result) < $nameLen - strlen($splitName[$i]) && $i < count($splitName))
        {
            $result = $result."_".$splitName[$i];
            $i++;
        }
        return mb_substr($result, 1);
    }
}