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
 * @property string|null $key_words
 *
 * @property ContractCategoryContract[] $contractCategoryContracts
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
            [['file', 'key_words'], 'string', 'max' => 1000],
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
            'key_words' => 'Key Words',
        ];
    }

    /**
     * Gets query for [[ContractCategoryContracts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContractCategoryContracts()
    {
        return $this->hasMany(ContractCategoryContract::className(), ['contract_id' => 'id']);
    }
}