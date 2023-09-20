<?php

namespace app\models\work;

use app\models\common\LegacyMaterialResponsibility;
use app\models\null\PeopleNull;
use app\models\work\PeopleWork;
use Yii;

/**
 */
class LegacyMaterialResponsibilityWork extends LegacyMaterialResponsibility
{
    public function getPeopleOutWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'people_out_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getPeopleInWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'people_in_id']);
        return $try->all() ? $try : new PeopleNull();
    }
}
