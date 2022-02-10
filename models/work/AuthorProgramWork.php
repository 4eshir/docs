<?php

namespace app\models\work;

use app\models\common\AuthorProgram;
use Yii;


class AuthorProgramWork extends AuthorProgram
{
    public function getAuthorWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'author_id']);
    }
}
