<?php

namespace app\models\work;

use app\models\common\CategoryContract;
use app\models\work\ContractCategoryContractWork;
use app\models\null\ContractCategoryContractNull;
use Yii;


class CategoryContractWork extends CategoryContract
{

    public function getContractCategoryContractsWork()
    {
        $try = $this->hasMany(ContractCategoryContractWork::className(), ['category_contract_id' => 'id']);
        return $try->all() ? $try : new ContractCategoryContractNull();
    }

}
