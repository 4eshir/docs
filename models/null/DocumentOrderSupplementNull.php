<?php

namespace app\models\null;

use app\models\work\DocumentOrderSupplementWork;

class DocumentOrderSupplementNull extends DocumentOrderSupplementWork
{
    function __construct()
    {
        $this->document_order_id = null;
        $this->foreign_event_goals_id = null;
        $this->compliance_document = null;
        $this->information_deadline = null;
        $this->input_deadline = null;
        $this->collector_id = null;
        $this->methodologist_id = null;
        $this->informant_id = null;
        $this->document_details = null;
        $this->contributor_id = null;
    }

}