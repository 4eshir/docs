<?php

namespace app\models\work;

use app\models\common\KindCharacteristic;
use app\models\null\CharacteristicObjectNull;
use Yii;


class KindCharacteristicWork extends KindCharacteristic
{
	public function getCharacteristicObjectWork()
    {
        $try = $this->hasOne(CharacteristicObjectWork::className(), ['id' => 'characteristic_object_id']);
        return $try->all() ? $try : new CharacteristicObjectNull();
    }
}
