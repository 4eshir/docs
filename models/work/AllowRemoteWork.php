<?php


namespace app\models\work;

use app\models\common\AllowRemote;
use Yii;


class AllowRemoteWork extends AllowRemote
{
    const FULLTIME = 1;
    const FULLTIME_WITH_REMOTE = 2;

    const ALL = [1, 2];
}