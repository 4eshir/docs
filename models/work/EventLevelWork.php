<?php

namespace app\models\work;

use app\models\common\EventLevel;
use Yii;


class EventLevelWork extends EventLevel
{
    const INTERNAL = 3;
    const DISTRICT = 4;
    const CITY = 5;
    const REGIONAL = 6;
    const FEDERAL = 7;
    const INTERNATIONAL = 8;

    const ALL = [3, 4, 5, 6, 7, 8];
}
