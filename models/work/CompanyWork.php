<?php

namespace app\models\common;

use Yii;


class CompanyWork extends Company
{
    public function checkForeignKeys()
    {
        $doc_out = DocumentOut::find()->where(['company_id' => $this->id])->all();
        $doc_in = DocumentIn::find()->where(['company_id' => $this->id])->all();
        $as = AsAdmin::find()->where(['as_company_id' => $this->id])->all();
        if (count($doc_out) > 0 || count($doc_in) > 0 || count($as) > 0)
        {

            Yii::$app->session->addFlash('error', 'Невозможно удалить организацию! Организация используется в документах');
            return false;
        }
        return true;
    }
}
