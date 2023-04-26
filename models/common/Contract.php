<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property int $id
 * @property string $date
 * @property string $number
 * @property string|null $file
 *
 * @property Invoice[] $invoices
 */
class Contract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'number'], 'required'],
            [['date'], 'safe'],
            [['number'], 'string', 'max' => 100],
            [['file'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'number' => 'Number',
            'file' => 'File',
        ];
    }

    /**
     * Gets query for [[Invoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['contract_id' => 'id']);
    }
}
