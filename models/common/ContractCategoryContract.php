<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "contract_category_contract".
 *
 * @property int $id
 * @property int $category_contract_id
 * @property int $contract_id
 *
 * @property CategoryContract $categoryContract
 * @property Contract $contract
 */
class ContractCategoryContract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract_category_contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_contract_id', 'contract_id'], 'required'],
            [['category_contract_id', 'contract_id'], 'integer'],
            [['category_contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryContract::className(), 'targetAttribute' => ['category_contract_id' => 'id']],
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
            'category_contract_id' => 'Category Contract ID',
            'contract_id' => 'Contract ID',
        ];
    }

    /**
     * Gets query for [[CategoryContract]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryContract()
    {
        return $this->hasOne(CategoryContract::className(), ['id' => 'category_contract_id']);
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
}
