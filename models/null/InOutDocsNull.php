<?php

namespace app\models\null;

use app\models\work\InOutDocsWork;

class InOutDocsNull extends InOutDocsWork
{
    function __construct()
    {
        $this->document_in_id = null;
        $this->date = null;
        $this->document_out_id = null;
        $this->people_id = null;
    }

}