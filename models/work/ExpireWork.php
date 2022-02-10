<?php

namespace app\models\work;

use app\models\common\Expire;
use Yii;


class ExpireWork extends Expire
{
    public function getExpireOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::className(), ['id' => 'expire_order_id']);
    }

    public function getExpireRegulationWork()
    {
        return $this->hasOne(RegulationWork::className(), ['id' => 'expire_regulation_id']);
    }

    public function getActiveRegulationWork()
    {
        return $this->hasOne(DocumentOrderWork::className(), ['id' => 'active_regulation_id']);
    }
}
