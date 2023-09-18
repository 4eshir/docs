<?php

namespace app\models\null;

use app\models\work\PeopleWork;

class PeopleNull extends PeopleWork
{
    function __construct()
    {
        $this->firstname = null;
        $this->secondname = null;
        $this->patronymic = null;
    }

}