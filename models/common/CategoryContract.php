<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "category_contract".
 *
 * @property int $id
 * @property string $name
 *
 * @property ContractCategoryContract[] $contractCategoryContracts
 */
class CategoryContract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category_contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[ContractCategoryContracts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContractCategoryContracts()
    {
        return $this->hasMany(ContractCategoryContract::className(), ['category_contract_id' => 'id']);
    }
}
