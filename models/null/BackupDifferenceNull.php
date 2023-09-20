<?php

namespace app\models\null;

use app\models\work\BackupDifferenceWork;

class BackupDifferenceNull extends BackupDifferenceWork
{
    function __construct()
    {
        $this->visit_id = null;
        $this->old_status = null;
        $this->new_status = null;
        $this->date = null;
    }

}