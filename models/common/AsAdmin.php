<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "as_admin".
 *
 * @property int $id
 * @property string $as_name
 * @property int $copyright_id
 * @property int $as_company_id
 * @property string $document_number
 * @property string $document_date
 * @property int $count
 * @property float $price
 * @property int $country_prod_id
 * @property int $unifed_register_number
 * @property int $distribution_type_id
 * @property int $license_id
 * @property string $comment
 * @property string $scan
 * @property string $license_file
 * @property string $commercial_offers
 * @property string $service_note
 * @property int $register_id
 * @property int $as_type_id
 *
 * @property Company $asCompany
 * @property Company $copyright
 * @property Country $countryProd
 * @property License $license
 * @property User $register
 * @property AsInstall[] $asInstalls
 * @property UseYears $useYears
 */
class AsAdmin extends \yii\db\ActiveRecord
{
    public $useYears;
    public $asInstalls;
    public $scanFile;
    public $licenseFile;
    public $commercialFiles;
    public $serviceNoteFile;
    public $useStartDate;
    public $useEndDate;
    public $requisits;

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
            [['scanFile'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true],
            [['licenseFile'], 'file', 'extensions' => 'png, jpg, pdf', 'skipOnEmpty' => true],
            [['serviceNoteFile'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['commercialFiles'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['as_name', 'as_company_id', 'count', 'country_prod_id', 'license_id', 'scan', 'register_id'], 'required'],
            [['as_company_id', 'count', 'country_prod_id', 'license_id', 'register_id', 'as_type_id', 'copyright_id', 'distribution_type_id'], 'integer'],
            [['document_date', 'license_start', 'license_finish', 'useStartDate', 'useEndDate'], 'safe'],
            [['price'], 'number'],
            [['comment', 'scan', 'as_name', 'service_note', 'document_number', 'unifed_register_number', 'requisits'], 'string', 'max' => 1000],
            [['as_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompany::className(), 'targetAttribute' => ['as_company_id' => 'id']],
            [['country_prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_prod_id' => 'id']],
            [['copyright_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompany::className(), 'targetAttribute' => ['copyright_id' => 'id']],
            [['license_id'], 'exist', 'skipOnError' => true, 'targetClass' => License::className(), 'targetAttribute' => ['license_id' => 'id']],
            [['distribution_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DistributionType::className(), 'targetAttribute' => ['license_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['register_id' => 'id']],

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
            'as_company_id' => 'As Company ID',
            'document_number' => 'Document Number',
            'document_date' => 'Document Date',
            'count' => 'Count',
            'price' => 'Price',
            'country_prod_id' => 'Country Prod ID',
            'license_start' => 'License Start',
            'license_finish' => 'License Finish',
            'license_id' => 'License ID',
            'comment' => 'Comment',
            'scan' => 'Scan',
            'service_note' => 'Service Note',
            'register_id' => 'Register ID',
        ];
    }

    /**
     * Gets query for [[AsCompany]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'as_company_id']);
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
     * Gets query for [[License]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLicense()
    {
        return $this->hasOne(License::className(), ['id' => 'license_id']);
    }

    /**
     * Gets query for [[Copyright]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCopyright()
    {
        return $this->hasOne(Company::className(), ['id' => 'copyright_id']);
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
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(User::className(), ['id' => 'register_id']);
    }

    public function getRequisits()
    {
        if ($this->document_number == null)
            return '';
        return $this->asCompany->name.' '.$this->document_number.' '.$this->document_date;
    }


    /**
     * Gets query for [[AsInstalls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsInstalls()
    {
        return $this->hasMany(AsInstall::className(), ['as_admin_id' => 'id']);
    }

    /**
     * Gets query for [[UseYears]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUseYears()
    {
        return $this->hasOne(UseYears::className(), ['as_admin_id' => 'id']);
    }

    public function GetNewId()
    {
        return AsAdmin::find()->orderBy('id DESC')->one()->id + 1;
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/as_admin/scan/';
        $name = $this->as_name;
        if (strlen($name) > 10) $name = mb_strimwidth($name, 0, 10);
        if ($this->id == null)
            $filename = 'Скан_'.$name.'_'.$this->GetNewId();
        else
            $filename = 'Скан_'.$name.'_'.$this->id;
        $filename = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $filename = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $filename);
        $this->scan = $filename . '.' . $this->scanFile->extension;
        $this->scanFile->saveAs($path . $filename . '.' . $this->scanFile->extension);
    }

    public function uploadLicenseFile()
    {
        $path = '@app/upload/files/as_admin/license/';
        $name = $this->as_name;
        if (strlen($name) > 10) $name = mb_strimwidth($name, 0, 10);
        if ($this->id == null)
            $filename = 'Лиц_'.$name.'_'.$this->GetNewId();
        else
            $filename = 'Лиц_'.$name.'_'.$this->id;
        $filename = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $filename = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $filename);
        $this->license_file = $filename . '.' . $this->licenseFile->extension;
        $this->licenseFile->saveAs($path . $filename . '.' . $this->licenseFile->extension);
    }

    public function uploadServiceNoteFiles($upd = null)
    {
        $result = '';
        $i = 1;
        foreach ($this->serviceNoteFile as $file) {
            $name = $this->as_name;
            if (strlen($name) > 10) $name = mb_strimwidth($name, 0, 10);
            $filename = '';
            if ($this->id == null)
                $filename = 'Служебная_'.$i.'_'.$name.'_'.$this->GetNewId();
            else
                $filename = 'Служебная_'.$i.'_'.$name.'_'.$this->id;
            $filename = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $filename = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $filename);

            $file->saveAs('@app/upload/files/as_admin/service_note/' . $filename . '.' . $file->extension);
            $result = $result . $filename . '.' . $file->extension . ' ';
            $i = $i + 1;
        }
        if ($upd == null)
            $this->service_note = $result;
        else
            $this->service_note = $this->service_note . $result;
        return true;
    }

    public function uploadCommercialFiles($upd = null)
    {
        $result = '';
        $i = 1;
        foreach ($this->commercialFiles as $file) {
            $name = $this->as_name;
            if (strlen($name) > 10) $name = mb_strimwidth($name, 0, 10);
            $filename = '';
            if ($this->id == null)
                $filename = 'КомПредложение_'.$i.'_'.$name.'_'.$this->GetNewId();
            else
                $filename = 'КомПредложение_'.$i.'_'.$name.'_'.$this->id;
            $filename = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $filename = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $filename);

            $file->saveAs('@app/upload/files/as_admin/commercial_files/' . $filename . '.' . $file->extension);
            $result = $result . $filename . '.' . $file->extension . ' ';
            $i = $i + 1;
        }
        if ($upd == null)
            $this->commercial_offers = $result;
        else
            $this->commercial_offers = $this->commercial_offers . $result;
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if ($this->asInstalls !== null)
            foreach ($this->asInstalls as $asInstallOne) {
                $asInstallOne->as_admin_id = $this->id;
                if ($asInstallOne->cabinet !== "" && $asInstallOne->count !== "")
                    $asInstallOne->save();
            }

        if ($this->useStartDate == null) $this->useStartDate = '1999-01-01';
        if ($this->useEndDate == null) $this->useEndDate = '1999-01-01';

        $use = new UseYears();
        $use->as_admin_id = $this->id;
        $use->start_date = $this->useStartDate;
        $use->end_date = $this->useEndDate;
        $use->save(false);



    }

    public function beforeDelete()
    {
        $useYears = UseYears::find()->where(['as_admin_id' => $this->id])->one();
        if ($useYears !== null)
            $useYears->delete();
        $asInstall = AsInstall::find()->where(['as_admin_id' => $this->id])->all();
        foreach ($asInstall as $asInstallOne) {
            $asInstallOne->delete();
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}