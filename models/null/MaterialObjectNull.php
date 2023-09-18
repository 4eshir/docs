<?php

namespace app\models\null;

use app\models\work\MaterialObjectWork;

class MaterialObjectNull extends MaterialObjectWork
{
    function __construct()
    {
        $this->name = null;
        $this->photo_local = null;
        $this->photo_cloud = null;
        $this->count = null;
        $this->price = null;
        $this->attribute = null;
        $this->finance_source_id = null;
        $this->inventory_number = null;
        $this->type = null;
        $this->kind_id = null;
        $this->is_education = null;
        $this->state = null;
        $this->damage = null;
        $this->status = null;
        $this->write_off = null;
        $this->lifetime = null;
        $this->expiration_date = null;
        $this->create_date = null;
        $this->characteristics = null;
        $this->objEntrId = null;
    }

}