<?php

namespace app\models\extended;

use app\models\common\DocumentOut;
use app\models\common\SendMethod;

class DocumentOutExtended extends DocumentOut
{
    public static function getAllDocOut()
    {
        return DocumentOut::find()->all();
    }

    public static function getDocByFilter($sm)
    {
        $smLocal = SendMethod::find()->where(['name' => $sm])->one();
        return DocumentOut::find()->where(['send_method_id' => $smLocal->id]);
    }
}