<?php

namespace app\models\null;

use app\models\work\ContainerWork;

class ContainerNull extends ContainerWork
{
    function __construct()
    {
        $this->name = null;
        $this->container_id = null;
        $this->material_object_id = null;
        $this->auditorium_id = null;
    }

}