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
 * @property string|null $comment
 *
 * @property AsAdmin[] $asAdmins
 * @property AsAdmin[] $asAdmins0
 * @property CategorySmsp $categorySmsp
 * @property CompanyType $companyType
 * @property Destination[] $destinations
 * @property DocumentIn[] $documentIns
 * @property DocumentOut[] $documentOuts
 * @property ForeignEvent[] $foreignEvents
 * @property Invoice[] $invoices
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
            [['company_type_id', 'is_contractor', 'category_smsp_id'], 'integer'],
            [['name', 'short_name'], 'required'],
            [['name', 'short_name', 'comment'], 'string', 'max' => 1000],
            [['inn'], 'string', 'max' => 15],
            [['category_smsp_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategorySmsp::className(), 'targetAttribute' => ['category_smsp_id' => 'id']],
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
            'is_contractor' => 'Is Contractor',
            'inn' => 'Inn',
            'category_smsp_id' => 'Category Smsp ID',
            'comment' => 'Comment',
        ];
    }

    /**
     * Gets query for [[AsAdmins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsAdmins()
    {
        return $this->hasMany(AsAdmin::className(), ['as_company_id' => 'id']);
    }

    /**
     * Gets query for [[AsAdmins0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsAdmins0()
    {
        return $this->hasMany(AsAdmin::className(), ['copyright_id' => 'id']);
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

    /**
     * Gets query for [[DocumentIns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns()
    {
        return $this->hasMany(DocumentIn::className(), ['company_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts()
    {
        return $this->hasMany(DocumentOut::className(), ['company_id' => 'id']);
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
     * Gets query for [[Invoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['contractor_id' => 'id']);
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
