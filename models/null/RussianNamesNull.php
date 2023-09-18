<?php

namespace app\models\null;

use app\models\work\RussianNamesWork;

class RussianNamesNull extends RussianNamesWork
{
    function __construct()
    {
        $this->Name = null;
        $this->Sex = null;
        $this->PeoplesCount = null;
        $this->WhenPeoplesCount = null;
        $this->Source = null;
    }

}