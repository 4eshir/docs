<?php

namespace app\models\null;

use app\models\work\UserWork;

class UserNull extends UserWork
{
    function __construct()
    {
        $this->secondname = null;
        $this->firstname = null;
        $this->patronymic = null;
        $this->username = null;
        $this->auth_key = null;
        $this->password_hash = null;
        //$this->creator_at = null;
        //$this->updated_at = null;
    }

}