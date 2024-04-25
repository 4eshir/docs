<?php

namespace app\models\null;

use app\models\work\DocumentOrderWork;

class DocumentOrderNull extends DocumentOrderWork
{
    function __construct()
    {
        $this->order_copy_id = null;
        $this->order_number = null;
        $this->order_name = null;
        $this->order_date = null;
        $this->scan = null;
        $this->creator_id = null;
        $this->order_postfix = null;
        $this->signed_id = null;
        $this->bring_id = null;
        $this->executor_id = null;
        $this->type = null;
        $this->state = null;
        $this->nomenclature_id = null;
        $this->doc = null;
        $this->key_words = null;
        $this->study_type = null;
    }

}