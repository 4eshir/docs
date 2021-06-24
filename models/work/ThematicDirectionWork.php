<?php

namespace app\models\work;

use app\models\common\ThematicDirection;
use Yii;


class ThematicDirectionWork extends ThematicDirection
{

    public function getTrueName()
    {
        return $this->full_name . ' (' . $this->name . ')';
    }
}
