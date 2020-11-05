<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "as_admin".
 *
 * @property int $id
 * @property string $as_name
 * @property int $as_company_id
 * @property int $document_number
 * @property string $document_date
 * @property int $count
 * @property float $price
 * @property int $country_prod_id
 * @property string $license_start
 * @property string $license_finish
 * @property int $version_id
 * @property int $license_id
 * @property string $comment
 * @property string $scan
 * @property string $service_note
 * @property int $register_id
 *
 * @property AsCompany $asCompany
 * @property Country $countryProd
 * @property License $license
 * @property User $register
 * @property Version $version
 * @property AsInstall[] $asInstalls
 * @property UseYears[] $useYears
 */
class AsAdmin extends \yii\db\ActiveRecord
{
    public $useYears;
    public $asInstalls;
    public $scanFile;
    public $serviceNoteFile;
    public $useStartDate;
    public $useEndDate;
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
            [['as_name', 'as_company_id', 'document_number', 'document_date', 'count', 'price', 'country_prod_id', 'license_start', 'license_finish', 'version_id', 'license_id', 'comment', 'scan', 'register_id'], 'required'],
            [['as_company_id', 'document_number', 'count', 'country_prod_id', 'version_id', 'license_id', 'register_id'], 'integer'],
            [['document_date', 'license_start', 'license_finish', 'useStartDate', 'useEndDate'], 'safe'],
            [['price'], 'number'],
            [['comment', 'scan', 'as_name', 'service_note'], 'string', 'max' => 1000],
            [['as_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompany::className(), 'targetAttribute' => ['as_company_id' => 'id']],
            [['country_prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_prod_id' => 'id']],
            [['license_id'], 'exist', 'skipOnError' => true, 'targetClass' => License::className(), 'targetAttribute' => ['license_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['version_id'], 'exist', 'skipOnError' => true, 'targetClass' => Version::className(), 'targetAttribute' => ['version_id' => 'id']],
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
            'version_id' => 'Version ID',
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
        return $this->hasOne(AsCompany::className(), ['id' => 'as_company_id']);
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
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(User::className(), ['id' => 'register_id']);
    }

    /**
     * Gets query for [[Version]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(Version::className(), ['id' => 'version_id']);
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
        return $this->hasMany(UseYears::className(), ['as_admin_id' => 'id']);
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/as_admin/scan/';
        do{
            $filename = Yii::$app->getSecurity()->generateRandomString(15);
        }while(file_exists('@app/upload/files/as_admin/scan/' . $filename . '.' . $this->scanFile->extension));
        $this->scan = $filename . '.' . $this->scanFile->extension;
        $this->scanFile->saveAs( $path . $filename . '.' . $this->scanFile->extension);
    }

    public function uploadServiceNoteFiles($upd = null)
    {
        $result = '';
        foreach ($this->serviceNoteFile as $file) {

            do{
                $filename = Yii::$app->getSecurity()->generateRandomString(15);
            }while(file_exists('@app/upload/files/as_admin/service_note/' . $filename . '.' . $file->extension));

            $file->saveAs('@app/upload/files/as_admin/service_note/' . $filename . '.' . $file->extension);
            $result = $result.$filename . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->service_note = $result;
        else
            $this->service_note = $this->service_note.$result;
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $years = $this->useYears;
        foreach ($years as $yearOne)
        {
            $yearOne->as_admin_id = $this->id;
            $yearOne->save();
        }

        foreach ($this->asInstalls as $asInstallOne)
        {
            $asInstallOne->as_admin_id = $this->id;
            $asInstallOne->save();
        }

        if ($this->useStartDate == null || $this->useEndDate == null)
        {
            $this->useStartDate = '1999-01-01';
            $this->useEndDate = '1999-01-01';
        }
        $use = new UseYears();
        $use->as_admin_id = $this->id;
        $use->start_date = $this->useStartDate;
        $use->end_date = $this->useEndDate;
        $use->save(false);
    }
}
