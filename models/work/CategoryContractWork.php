<?php

namespace app\models\work;

use app\models\common\CategoryContract;
use app\models\work\ContractCategoryContractWork;
use Yii;


class CategoryContractWork extends CategoryContract
{

    public function getContractCategoryContractsWork()
    {
        return $this->hasMany(ContractCategoryContractWork::className(), ['category_contract_id' => 'id']);
    }
}
