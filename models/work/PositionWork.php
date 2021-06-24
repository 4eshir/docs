<?php

namespace app\models\work;

use app\models\common\DocumentIn;
use app\models\common\DocumentOut;
use app\models\common\People;
use app\models\common\Position;
use app\models\components\Logger;
use Yii;


class PositionWork extends Position
{

    public function checkForeignKeys()
    {
        $doc_out = DocumentOut::find()->where(['position_id' => $this->id])->all();
        $doc_in = DocumentIn::find()->where(['position_id' => $this->id])->all();
        $people = People::find()->where(['position_id' => $this->id])->all();
        if (count($doc_out) > 0 || count($doc_in) > 0 || count($people) > 0)
        {

            Yii::$app->session->addFlash('error', 'Невозможно удалить должность! Должность используется в документах и/или является должностью человека в системе');
            return false;
        }
        return true;
    }

}
