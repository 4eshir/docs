<?php

namespace app\models\work;

use app\models\common\HistoryTransaction;
use app\models\common\People;
use app\models\common\User;
use Yii;


class HistoryTransactionWork extends HistoryTransaction
{
    public function getPeopleGetWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'people_get_id']);
    }

    /**
     * Gets query for [[PeopleGive]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleGiveWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'people_give_id']);
    }
}
