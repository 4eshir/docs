<?php

namespace app\models\work;

use app\models\common\GroupErrors;
use app\models\work\ErrorsWork;
use Yii;


class GroupErrorsWork extends GroupErrors
{
    public function CheckErrors ($modelGroup)
    {
        var_dump($modelGroup->id);
        $group_id = $modelGroup->id;

        $teacher_id = $modelGroup->teacher_id;
        if ($teacher_id == 0)
        {
            $this->training_group_id = $group_id;
            $this->errors_id = 1;
        }

        var_dump($this->training_group_id);
        var_dump($this->errors_id);
    }
}
