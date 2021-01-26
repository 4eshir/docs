<?php


namespace app\models\components;


class FileWizard
{
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
}