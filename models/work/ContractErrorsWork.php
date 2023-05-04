<?php

namespace app\models\work;

use Yii;
use app\models\common\ContractErrors;

class ContractErrorsWork extends ContractErrors
{
    public function EventAmnesty ($modelEventID)
    {
        $errors = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelEventID)
    {
        $errors = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }


}
