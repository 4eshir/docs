<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "entry".
 *
 * @property int $id
 * @property int $object_id
 * @property int $amount
 *
 * @property MaterialObject $object
 * @property InvoiceEntry[] $invoiceEntries
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
            [['object_id', 'amount'], 'required'],
            [['object_id', 'amount'], 'integer'],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_id' => 'Object ID',
            'amount' => 'Amount',
        ];
    }

    /**
     * Gets query for [[Object]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(MaterialObject::className(), ['id' => 'object_id']);
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
}
