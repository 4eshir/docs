<?php

namespace app\models\work;

use app\models\common\ObjectCharacteristic;
use Yii;


class ObjectCharacteristicWork extends ObjectCharacteristic
{
	public function getMaterialObjectWork()
    {
        return $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
    }

    public function getCharacteristicObjectWork()
    {
        return $this->hasOne(CharacteristicObjectWork::className(), ['id' => 'characteristic_object_id']);
    }

    public function getValue()
    {
    	if ($this->integer_value !== null) return $this->integer_value;
    	if ($this->double_value !== null) return $this->double_value;
    	if ($this->string_value !== null && strlen($this->string_value) > 0) return $this->string_value;
    }
}
