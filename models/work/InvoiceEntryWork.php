<?php

namespace app\models\work;

use app\models\common\InvoiceEntry;
use Yii;


class InvoiceEntryWork extends InvoiceEntry
{
	public function getEntryWork()
    {
        return $this->hasOne(EntryWork::className(), ['id' => 'entry_id']);
    }

    /**
     * Gets query for [[Invoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceWork()
    {
        return $this->hasOne(InvoiceWork::className(), ['id' => 'invoice_id']);
    }


    public function afterDelete()
    {
        $entries = EntryWork::find()->where(['id' => $this->entry_id])->all();

        foreach ($entries as $one)
            $one->delete();

        return parent::afterDelete();
    }
}
