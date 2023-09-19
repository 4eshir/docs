<?php

namespace app\models\null;

use app\models\work\DocumentInWork;

class DocumentInNull extends DocumentInWork
{
    function __construct()
    {
        $this->local_date = null;
        $this->real_date = null;
        $this->send_method_id = null;
        $this->position_id = null;
        $this->company_id = null;
        $this->document_theme = null;
        $this->signed_id = null;
        $this->target = null;
        $this->get_id = null;
        $this->register_id = null;
        $this->local_number = null;
        $this->correspondent_id = null;
        $this->local_postfix = null;
        $this->needAnswer = null;
        $this->scan = null;
        $this->applications = null;
        $this->key_words = null;
        $this->doc = null;
        $this->real_number = null;
    }

}