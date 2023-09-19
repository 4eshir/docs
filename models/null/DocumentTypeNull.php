<?php

namespace app\models\null;

use app\models\work\DocumentTypeWork;

class DocumentTypeNull extends DocumentTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}