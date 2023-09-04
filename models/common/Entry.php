<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "entry".
 *
 * @property int $id
 * @property int $amount
 *
 * @property InvoiceEntry[] $invoiceEntries
 * @property ObjectEntry[] $objectEntries
 */
class Entry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Amount',
        ];
    }

    /**
     * Gets query for [[InvoiceEntries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceEntries()
    {
        return $this->hasMany(InvoiceEntry::className(), ['entry_id' => 'id']);
    }

    /**
     * Gets query for [[ObjectEntries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectEntries()
    {
        return $this->hasMany(ObjectEntry::className(), ['entry_id' => 'id']);
    }
}
