<?php

namespace app\models\common;

use app\models\work\PeopleWork;
use Yii;

/**
 */
class LegacyMaterialResponsibilityWork extends LegacyMaterialResponsibility
{
    public function getPeopleOutWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'people_out_id']);
    }

    public function getPeopleInWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'people_in_id']);
    }
}
