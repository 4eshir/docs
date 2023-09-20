<?php

namespace app\models\null;

use app\models\work\CertificatWork;

class CertificatNull extends CertificatWork
{
    function __construct()
    {
        $this->certificat_number = null;
        $this->certificat_template_id = null;
        $this->training_group_participant_id = null;
        $this->status = null;
    }

}