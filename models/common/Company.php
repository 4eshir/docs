<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int|null $company_type_id
 * @property string $name
 * @property string $short_name
 * @property int $is_contractor
 * @property string|null $inn
 * @property int|null $category_smsp_id
 * @property int|null $ownership_type_id
 * @property string|null $okved
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $site
 * @property string|null $head_fio
 * @property string|null $comment
 * @property int|null $last_edit_id
 *
 * @property CompanyType $companyType
 * @property CategorySmsp $categorySmsp
 * @property OwnershipType $ownershipType
 * @property User $lastEdit
 * @property ForeignEvent[] $foreignEvents
 * @property People[] $peoples
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
            [['company_type_id', 'is_contractor', 'category_smsp_id', 'ownership_type_id', 'last_edit_id'], 'integer'],
            [['name', 'short_name', 'is_contractor'], 'required'],
            [['name', 'short_name', 'email', 'site', 'head_fio', 'comment'], 'string', 'max' => 1000],
            [['inn'], 'string', 'max' => 15],
            [['okved', 'phone_number'], 'string', 'max' => 12],
            [['company_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompanyType::className(), 'targetAttribute' => ['company_type_id' => 'id']],
            [['category_smsp_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategorySmsp::className(), 'targetAttribute' => ['category_smsp_id' => 'id']],
            [['ownership_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => OwnershipType::className(), 'targetAttribute' => ['ownership_type_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'is_contractor' => 'Is Contractor',
            'inn' => 'Inn',
            'category_smsp_id' => 'Category Smsp ID',
            'ownership_type_id' => 'Ownership Type ID',
            'okved' => 'Okved',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'site' => 'Site',
            'head_fio' => 'Head Fio',
            'comment' => 'Comment',
            'last_edit_id' => 'Last Edit ID',
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
     * Gets query for [[CategorySmsp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategorySmsp()
    {
        return $this->hasOne(CategorySmsp::className(), ['id' => 'category_smsp_id']);
    }

    /**
     * Gets query for [[OwnershipType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwnershipType()
    {
        return $this->hasOne(OwnershipType::className(), ['id' => 'ownership_type_id']);
    }

    /**
     * Gets query for [[LastEdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEdit()
    {
        return $this->hasOne(User::className(), ['id' => 'last_edit_id']);
    }

    /**
     * Gets query for [[ForeignEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvents()
    {
        return $this->hasMany(ForeignEvent::className(), ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Peoples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeoples()
    {
        return $this->hasMany(People::className(), ['company_id' => 'id']);
    }
}
