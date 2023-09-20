<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\LegacyResponsible;
use app\models\null\DocumentOrderNull;
use app\models\null\PeopleNull;
use Yii;


class LegacyResponsibleWork extends LegacyResponsible
{
    public function getPeopleWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'people_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getOrderWork()
    {
        $try = $this->hasOne(DocumentOrderWork::className(), ['id' => 'order_id']);
        return $try->all() ? $try : new DocumentOrderNull();
    }
}
