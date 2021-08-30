<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\LegacyResponsible;
use Yii;


class LegacyResponsibleWork extends LegacyResponsible
{
    public function getPeopleWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'people_id']);
    }

    public function getOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::className(), ['id' => 'order_id']);
    }
}
