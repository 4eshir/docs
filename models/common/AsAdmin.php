<?php

namespace app\models\common;

use DateTime;
use Yii;

/**
 * This is the model class for table "as_admin".
 *
 * @property int $id
 * @property string $as_name
 * @property int|null $copyright_id
 * @property int $as_company_id
 * @property string $document_number
 * @property string $document_date
 * @property int $count
 * @property float $price
 * @property int $country_prod_id
 * @property string|null $unifed_register_number
 * @property int|null $distribution_type_id
 * @property int $license_id
 * @property string|null $comment
 * @property string $scan
 * @property string|null $license_file
 * @property string|null $commercial_offers
 * @property string $service_note
 * @property int $register_id
 * @property int $as_type_id
 * @property string|null $contract_subject
 * @property int|null $license_count
 * @property int|null $license_type_id
 * @property int|null $license_term_type_id
 * @property int $license_status
 *
 * @property Company $asCompany
 * @property Company $copyright
 * @property Country $countryProd
 * @property DistributionType $distributionType
 * @property License $license
 * @property User $register
 * @property AsType $asType
 * @property LicenseTermType $licenseType
 * @property LicenseTermType $licenseTermType
 */
class AsAdmin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'as_admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['as_name', 'as_company_id', 'document_number', 'document_date', 'count', 'price', 'country_prod_id', 'license_id', 'scan', 'service_note', 'register_id', 'as_type_id'], 'required'],
            [['copyright_id', 'as_company_id', 'count', 'country_prod_id', 'distribution_type_id', 'license_id', 'register_id', 'as_type_id', 'license_count', 'license_term_type_id', 'license_type_id', 'license_status'], 'integer'],
            [['document_date', 'useStartDate', 'useEndDate'], 'safe'],
            [['price'], 'number'],
            [['as_name', 'document_number', 'unifed_register_number', 'comment', 'scan', 'license_file', 'commercial_offers', 'service_note', 'contract_subject'], 'string', 'max' => 1000],
            [['as_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompany::className(), 'targetAttribute' => ['as_company_id' => 'id']],
            [['copyright_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompany::className(), 'targetAttribute' => ['copyright_id' => 'id']],
            [['country_prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_prod_id' => 'id']],
            [['distribution_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DistributionType::className(), 'targetAttribute' => ['distribution_type_id' => 'id']],
            [['license_id'], 'exist', 'skipOnError' => true, 'targetClass' => License::className(), 'targetAttribute' => ['license_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['as_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsType::className(), 'targetAttribute' => ['as_type_id' => 'id']],
            [['license_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LicenseType::className(), 'targetAttribute' => ['license_type_id' => 'id']],
            [['license_term_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LicenseTermType::className(), 'targetAttribute' => ['license_term_type_id' => 'id']],
            [['scanFile'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true],
            [['licenseFile'], 'file', 'extensions' => 'png, jpg, pdf', 'skipOnEmpty' => true],
            [['serviceNoteFile'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['commercialFiles'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'as_name' => 'As Name',
            'copyright_id' => 'Copyright ID',
            'as_company_id' => 'As Company ID',
            'document_number' => 'Document Number',
            'document_date' => 'Document Date',
            'count' => 'Count',
            'price' => 'Price',
            'country_prod_id' => 'Country Prod ID',
            'unifed_register_number' => 'Unifed Register Number',
            'distribution_type_id' => 'Distribution Type ID',
            'license_id' => 'License ID',
            'comment' => 'Comment',
            'scan' => 'Scan',
            'license_file' => 'License File',
            'commercial_offers' => 'Commercial Offers',
            'service_note' => 'Service Note',
            'register_id' => 'Register ID',
            'as_type_id' => 'As Type ID',
            'contract_subject' => 'Contract Subject',
            'license_count' => 'License Count',
            'license_type_id' => 'License Type ID',
            'license_term_type_id' => 'License Term Type ID',
            'license_status' => 'Лицензия активна'
        ];
    }

    /**
     * Gets query for [[AsCompany]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsCompany()
    {
        return $this->hasOne(AsCompany::className(), ['id' => 'as_company_id']);
    }

    /**
     * Gets query for [[Copyright]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCopyright()
    {
        return $this->hasOne(AsCompany::className(), ['id' => 'copyright_id']);
    }

    /**
     * Gets query for [[CountryProd]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountryProd()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_prod_id']);
    }

    /**
     * Gets query for [[DistributionType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistributionType()
    {
        return $this->hasOne(DistributionType::className(), ['id' => 'distribution_type_id']);
    }

    /**
     * Gets query for [[License]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLicense()
    {
        return $this->hasOne(License::className(), ['id' => 'license_id']);
    }

    public function getLicenseType()
    {
        return $this->hasOne(LicenseType::className(), ['id' => 'license_type_id']);
    }

    /**
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(People::className(), ['id' => 'register_id']);
    }

    /**
     * Gets query for [[AsType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsType()
    {
        return $this->hasOne(AsType::className(), ['id' => 'as_type_id']);
    }

    /**
     * Gets query for [[LicenseTermType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLicenseTermType()
    {
        return $this->hasOne(LicenseTermType::className(), ['id' => 'license_term_type_id']);
    }
}
