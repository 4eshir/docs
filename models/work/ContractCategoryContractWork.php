<?php

namespace app\models\work;

use app\models\common\ContractCategoryContract;
use app\models\work\CategoryContractWork;
use app\models\work\ContractWork;
use app\models\null\CategoryContractNull;
use app\models\null\ContractNull;
use Yii;

class ContractCategoryContractWork extends ContractCategoryContract
{

    public function rules()
    {
        return [
            [['category_contract_id', 'contract_id'], 'required'],
            [['category_contract_id', 'contract_id'], 'integer'],
            [['category_contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryContractWork::className(), 'targetAttribute' => ['category_contract_id' => 'id']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractWork::className(), 'targetAttribute' => ['contract_id' => 'id']],
        ];
    }

    public function getCategoryContractWork()
    {
        $try = $this->hasOne(CategoryContractWork::className(), ['id' => 'category_contract_id']);
        return $try->all() ? $try : new CategoryContractNull();
    }

    public function getContractWork()
    {
        $try = $this->hasOne(ContractWork::className(), ['id' => 'contract_id']);
        return $try->all() ? $try : new ContractNull();
    }
}
