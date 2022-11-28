<?php

namespace app\models\work;

use app\models\common\ThematicDirection;
use Yii;


class ThematicDirectionWork extends ThematicDirection
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Краткое наименование',
            'full_name' => 'Полное наименование',
        ];
    }

    public function getTrueName()
    {
        return $this->full_name . ' (' . $this->name . ')';
    }
}
