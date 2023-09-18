<?php

namespace app\models\work;

use app\models\common\AuthorProgram;
use app\models\null\PeopleNull;
use Yii;


class AuthorProgramWork extends AuthorProgram
{
    public function getAuthorWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'author_id']);
        return $try->all() ? $try : new PeopleNull();
    }
}
