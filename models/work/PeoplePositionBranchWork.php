<?php

namespace app\models\work;

use app\models\common\PeoplePositionBranch;
use app\models\null\PositionNull;
use Yii;


class PeoplePositionBranchWork extends PeoplePositionBranch
{
    public function getPositionWork()
    {
        $try = $this->hasOne(PositionWork::className(), ['id' => 'position_id']);
        return $try->all() ? $try : new PositionNull();
    }
}
