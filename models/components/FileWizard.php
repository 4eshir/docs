<?php


namespace app\models\components;


class FileWizard
{
    static public function CutFilename($filename)
    {
        $result = '';
        $splitName = explode("_", $filename);
        $i = 0;
        while (strlen($result) < 250 - strlen($splitName[$i]))
        {
            $result = $result."_".$splitName[$i];
            $i++;
        }
        return mb_substr($result, 1);
    }
}