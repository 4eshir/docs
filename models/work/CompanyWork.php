<?php

namespace app\models\work;

use app\models\common\AsAdmin;
use app\models\common\Company;
use app\models\common\DocumentIn;
use app\models\common\DocumentOut;
use Yii;


class CompanyWork extends Company
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_type_id' => 'Company Type ID',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'is_contractor' => 'Является контрагентом',
            'inn' => 'Inn',
            'category_smsp_id' => 'Category Smsp ID',
            'comment' => 'Комментарий',
            'phone_number' => 'Номер телефона',
            'email' => 'E-mail',
            'site' => 'Сайт (при наличии)',
        ];
    }

    public function getCategorySmspString()
    {
        $model = CategorySmspWork::find()->where(['id' => $this->category_smsp_id])->one();
        return $model->name;
    }

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
