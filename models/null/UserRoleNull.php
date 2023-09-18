<?php

namespace app\models\null;

use app\models\work\UserRoleWork;

class UserRoleNull extends UserRoleWork
{
    function __construct()
    {
        $this->user_id = null;
        $this->role_id = null;
    }

}