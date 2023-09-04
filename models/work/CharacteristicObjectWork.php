<?php

namespace app\models\work;

use app\models\common\CharacteristicObject;
use Yii;


class CharacteristicObjectWork extends CharacteristicObject
{
    public $dd_value;

    public function rules()
    {
        return [
            [['name', 'value_type'], 'required'],
            [['value_type'], 'integer'],
            [['name', 'dd_value'], 'string', 'max' => 1000],
        ];
    }

    public function getValueTypeStr()
    {
        switch ($this->value_type)
        {
            case 1: return 'Целое число';
            case 2: return 'Дробное число';
            case 3: return 'Строковое значение';
            case 4: return 'Булевое';
            case 5: return 'Дата';
            case 6: return 'Файл';
            case 7: return 'Выпадающий список';
            default: return 'WHAT?';
        }
    }

    public function getDdValueStr()
    {
        $items = DropdownCharacteristicObjectWork::find()->where(['characteristic_object_id' => $this->id])->all();
        $res = '';

        foreach ($items as $item) {
            $res .= $item->item.', ';
        }

        return substr($res, 0, -2);
    }

}
