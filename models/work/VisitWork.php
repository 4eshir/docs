<?php

namespace app\models\work;

use app\models\common\Visit;
use Yii;


class VisitWork extends Visit
{
    public function getPrettyStatus()
    {
        if ($this->status == 1)
            return '<td style="background-color: #DC143C"><font color=white>Н</font></td>';
        else if ($this->status == 2)
            return '<td style="background-color: #183BD9"><font color=white>Д</font></td>';
        else if ($this->status == 3)
            return '<td>--</td>';
        else
            return '<td style="background-color: green"><font color=white>Я</font></td>';
    }
}
