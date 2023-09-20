<?php

namespace app\models\null;

use app\models\work\AsInstallWork;

class AsInstallNull extends AsInstallWork
{
    function __construct()
    {
        $this->install_place_id = null;
        $this->as_admin_id = null;
        $this->cabinet = null;
        $this->count = null;
    }

}