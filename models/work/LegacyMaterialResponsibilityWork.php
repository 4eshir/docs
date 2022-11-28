<?php

namespace app\models\work;

use app\models\common\LegacyMaterialResponsibility;
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
