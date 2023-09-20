<?php

namespace app\models\null;

use app\models\work\AuditoriumWork;

class AuditoriumNull extends AuditoriumWork
{
    function __construct()
    {
        $this->name = null;
        $this->square = null;
        $this->branch_id = null;
        $this->capacity = null;
        $this->is_education = null;
        $this->include_square = null;
        $this->window_count = null;
        $this->auditorium_type_id = null;
        $this->text = null;
        $this->files = null;
    }

}