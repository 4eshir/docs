<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int $company_type_id
 * @property string $name
 * @property string $short_name
 *
 * @property CompanyType $companyType
 * @property Destination[] $destinations
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_type_id', 'name'], 'required'],
            [['company_type_id'], 'integer'],
            [['name', 'short_name'], 'string', 'max' => 1000],
            [['company_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompanyType::className(), 'targetAttribute' => ['company_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_type_id' => 'Company Type ID',
            'name' => 'Name',
            'short_name' => 'Short Name',
        ];
    }

    /**
     * Gets query for [[CompanyType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyType()
    {
        return $this->hasOne(CompanyType::className(), ['id' => 'company_type_id']);
    }

    /**
     * Gets query for [[Destinations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDestinations()
    {
        return $this->hasMany(Destination::className(), ['company_id' => 'id']);
    }
}
