<?php

namespace app\models\work;

use app\models\common\Contract;
use Yii;
use app\models\common\ContractErrors;

class ContractErrorsWork extends ContractErrors
{
    public function ContractAmnesty ($modelContractID)
    {
        $errors = ContractErrorsWork::find()->where(['contract_id' => $modelContractID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelContractID)
    {
        $errors = ContractErrorsWork::find()->where(['contract_id' => $modelContractID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    public function getCritical()
    {
        return $this->critical;
    }

    private function CheckCategory ($modelContractID)
    {
        $err = ContractErrorsWork::find()->where(['contract_id' => $modelContractID, 'time_the_end' => null, 'errors_id' => 46])->all();
        $category = ContractCategoryContractWork::find()->where(['contract_id' => $modelContractID])->all();

        foreach ($err as $oneErr)
        {
            if (count($category) > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && count($category) === 0)
        {
            $this->contract_id = $modelContractID;
            $this->errors_id = 46;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckKeyWords ($modelContractID, $contract)
    {
        $err = ContractErrorsWork::find()->where(['contract_id' => $modelContractID, 'time_the_end' => null, 'errors_id' => 47])->all();

        foreach ($err as $oneErr)
        {
            if ($contract->key_words !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $contract->key_words === '')
        {
            $this->contract_id = $modelContractID;
            $this->errors_id = 47;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckFile ($modelContractID, $contract)
    {
        $err = ContractErrorsWork::find()->where(['contract_id' => $modelContractID, 'time_the_end' => null, 'errors_id' => 48])->all();

        foreach ($err as $oneErr)
        {
            if ($contract->file !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $contract->file === '')
        {
            $this->contract_id = $modelContractID;
            $this->errors_id = 48;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsContract ($modelContractID)
    {
        $contract = ContractWork::find()->where(['id' => $modelContractID])->one();

        $this->CheckCategory($modelContractID);
        $this->CheckKeyWords($modelContractID, $contract);
        $this->CheckFile($modelContractID, $contract);
    }

    public function CheckErrorsContractWithoutAmnesty ($modelContractID)
    {
        $this->NoAmnesty($modelContractID);
        $this->CheckErrorsContract($modelContractID);
    }
}
