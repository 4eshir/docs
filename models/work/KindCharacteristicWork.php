<?php

namespace app\models\work;

use app\models\common\KindCharacteristic;
use Yii;


class KindCharacteristicWork extends KindCharacteristic
{
	public function getCharacteristicObjectWork()
    {
        return $this->hasOne(CharacteristicObjectWork::className(), ['id' => 'characteristic_object_id']);
    }
}
