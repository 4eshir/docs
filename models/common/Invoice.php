<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string $number
 * @property int $contractor_id
 * @property string $date
 * @property int $type 0 - не является контрагентом, 1 - является контрагентом
 *
 * @property Company $contractor
 * @property InvoiceEntry[] $invoiceEntries
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'contractor_id', 'date'], 'required'],
            [['contractor_id', 'type'], 'integer'],
            [['date'], 'safe'],
            [['number'], 'string', 'max' => 15],
            [['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['contractor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'contractor_id' => 'Contractor ID',
            'date' => 'Date',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Contractor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Company::className(), ['id' => 'contractor_id']);
    }

    /**
     * Gets query for [[InvoiceEntries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceEntries()
    {
        return $this->hasMany(InvoiceEntry::className(), ['invoice_id' => 'id']);
    }
}
