<?php

namespace app\models\null;

use app\models\work\CompanyWork;

class CompanyNull extends CompanyWork
{
    function __construct()
    {
        $this->company_type_id = null;
        $this->is_contractor = null;
        $this->category_smsp_id = null;
        $this->ownership_type_id = null;
        $this->last_edit_id = null;
        $this->name = null;
        $this->short_name = null;
        $this->email = null;
        $this->site = null;
        $this->head_fio = null;
        $this->comment = null;
        $this->inn = null;
        $this->okved = null;
        $this->phone_number = null;
    }

}