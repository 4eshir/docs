<?php

namespace app\models\null;

use app\models\work\ContractCategoryContractWork;

class ContractCategoryContractNull extends ContractCategoryContractWork
{
    function __construct()
    {
        $this->category_contract_id = null;
        $this->contract_id = null;
    }

}