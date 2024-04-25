<?php

namespace app\models\work;

use app\models\common\Expire;
use app\models\null\DocumentOrderNull;
use app\models\null\RegulationNull;
use Yii;


class ExpireWork extends Expire
{
    public function getExpireOrderWork()
    {
        $try = $this->hasOne(DocumentOrderWork::className(), ['id' => 'expire_order_id']);
        return $try->all() ? $try : new DocumentOrderNull();
    }

    public function getExpireRegulationWork()
    {
        $try = $this->hasOne(RegulationWork::className(), ['id' => 'expire_regulation_id']);
        return $try->all() ? $try : new RegulationNull();
    }

    public function getActiveRegulationWork()
    {
        $try = $this->hasOne(RegulationWork::className(), ['id' => 'active_regulation_id']);
        return $try->all() ? $try : new RegulationNull();
    }
}
