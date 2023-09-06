<?php

namespace app\models\work;

use app\models\common\PeoplePositionBranch;
use Yii;


class PeoplePositionBranchWork extends PeoplePositionBranch
{
    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::className(), ['id' => 'position_id']);
    }
}
