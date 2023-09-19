<?php

namespace app\models\null;

use app\models\work\DocumentOutWork;

class DocumentOutNull extends DocumentOutWork
{
    function __construct()
    {
        $this->document_name = null;
        $this->document_date = null;
        $this->send_method_id = null;
        $this->position_id = null;
        $this->company_id = null;
        $this->document_theme = null;
        $this->signed_id = null;
        $this->register_id = null;
        $this->correspondent_id = null;
        $this->applications = null;
        $this->key_words = null;
        $this->doc = null;
        $this->document_postfix = null;
        $this->executor_id = null;
        $this->sent_date = null;
        $this->Scan = null;
        $this->isAnswer = null;
    }

}