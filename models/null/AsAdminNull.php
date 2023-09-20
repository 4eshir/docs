<?php

namespace app\models\null;

use app\models\work\AsAdminWork;

class AsAdminNull extends AsAdminWork
{
    function __construct()
    {
        $this->as_name = null;
        $this->as_company_id = null;
        $this->document_number = null;
        $this->document_date = null;
        $this->count = null;
        $this->price = null;
        $this->country_prod_id = null;
        $this->license_id = null;
        $this->scan = null;
        $this->service_note = null;
        $this->register_id = null;
        $this->as_type_id = null;
        $this->copyright_id = null;
        $this->useStartDate = null;
        $this->useEndDate = null;
        $this->distribution_type_id = null;
        $this->license_count = null;
        $this->license_term_type_id = null;
        $this->license_type_id = null;
        $this->license_status = null;
        $this->unifed_register_number = null;
        $this->comment = null;
        $this->commercial_offers = null;
        $this->license_file = null;
        $this->contract_subject = null;
    }

}