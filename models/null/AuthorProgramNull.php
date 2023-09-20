<?php

namespace app\models\null;

use app\models\work\AuthorProgramWork;

class AuthorProgramNull extends AuthorProgramWork
{
    function __construct()
    {
        $this->training_program_id = null;
        $this->author_id = null;
    }

}