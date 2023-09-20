<?php

namespace app\models\null;

use app\models\work\ContainerObjectWork;

class ContainerObjectNull extends ContainerObjectWork
{
    function __construct()
    {
        $this->container_id = null;
        $this->material_object_id = null;
    }

}