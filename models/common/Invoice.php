<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string $number
 * @property int $contractor_id
 * @property string $date_product
 * @property string $date_invoice
 * @property int $type 0 - накладная, 1 - акт, 2 - УПД, 3 - протокол
 
 * @property string|null $document
 * @property int|null $contract_id
 *
 * @property Company $contractor
 * @property Contract $contract
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
            [['number', 'contractor_id', 'date_product', 'date_invoice'], 'required'],
            [['contractor_id', 'type', 'contract_id'], 'integer'],
            [['date_product', 'date_invoice'], 'safe'],
            [['number'], 'string', 'max' => 15],
            [['document'], 'string', 'max' => 1000],
            [['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['contractor_id' => 'id']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::className(), 'targetAttribute' => ['contract_id' => 'id']],
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
            'date_product' => 'Date Product',
            'date_invoice' => 'Date Invoice',
            'type' => 'Type',
            'document' => 'Document',
            'contract_id' => 'Contract ID',
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

    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'contractor_id']);
    }

    /**
     * Gets query for [[Contract]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
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
