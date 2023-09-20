<?php

namespace app\models\work;

use app\models\common\HistoryTransaction;
use app\models\common\People;
use app\models\common\User;
use app\models\null\PeopleNull;
use Yii;


class HistoryTransactionWork extends HistoryTransaction
{
    public function getPeopleGetWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'people_get_id']);
        return $try->all() ? $try : new PeopleNull();
    }


    public function getPeopleGiveWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'people_give_id']);
        return $try->all() ? $try : new PeopleNull();
    }
}
