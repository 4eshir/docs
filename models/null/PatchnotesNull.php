<?php

namespace app\models\null;

use app\models\work\PatchnotesWork;

class PatchnotesNull extends PatchnotesWork
{
    function __construct()
    {
        $this->first_number = null;
        $this->second_number = null;
        $this->date = null;
        $this->text = null;
    }

}