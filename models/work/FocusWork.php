<?php

namespace app\models\work;

use app\models\common\Focus;
use Yii;


class FocusWork extends Focus
{
    const TECHNICAL = 1;
    const ART = 2;
    const SOCIAL = 3;
    const SCIENCE = 4;
    const SPORT = 5;

    const ALL = [1, 2, 3, 4, 5];
}
