<?php

namespace app\models\work;

use app\models\common\AsAdmin;
use app\models\common\AsInstall;
use app\models\common\UseYears;
use DateTime;
use Yii;


class AsAdminWork extends AsAdmin
{
    public $useStartDate;
    public $useEndDate;
    public $scanFile;
    public $licenseFile;
    public $commercialFiles;
    public $serviceNoteFile;
    public $asInstalls;

    public function rules()
    {
        return [
            [['as_name', 'as_company_id', 'document_number', 'document_date', 'count', 'price', 'country_prod_id', 'license_id', 'scan', 'service_note', 'register_id', 'as_type_id'], 'required'],
            [['copyright_id', 'as_company_id', 'count', 'country_prod_id', 'distribution_type_id', 'license_id', 'register_id', 'as_type_id', 'license_count', 'license_term_type_id', 'license_type_id', 'license_status'], 'integer'],
            [['document_date', 'useStartDate', 'useEndDate'], 'safe'],
            [['price'], 'number'],
            [['as_name', 'document_number', 'unifed_register_number', 'comment', 'scan', 'license_file', 'commercial_offers', 'service_note', 'contract_subject'], 'string', 'max' => 1000],
            [['as_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompanyWork::className(), 'targetAttribute' => ['as_company_id' => 'id']],
            [['copyright_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsCompanyWork::className(), 'targetAttribute' => ['copyright_id' => 'id']],
            [['country_prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => CountryWork::className(), 'targetAttribute' => ['country_prod_id' => 'id']],
            [['distribution_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DistributionTypeWork::className(), 'targetAttribute' => ['distribution_type_id' => 'id']],
            [['license_id'], 'exist', 'skipOnError' => true, 'targetClass' => LicenseWork::className(), 'targetAttribute' => ['license_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleWork::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['as_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsTypeWork::className(), 'targetAttribute' => ['as_type_id' => 'id']],
            [['license_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LicenseTypeWork::className(), 'targetAttribute' => ['license_type_id' => 'id']],
            [['license_term_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LicenseTermTypeWork::className(), 'targetAttribute' => ['license_term_type_id' => 'id']],
            [['scanFile'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true],
            [['licenseFile'], 'file', 'extensions' => 'png, jpg, pdf', 'skipOnEmpty' => true],
            [['serviceNoteFile'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['commercialFiles'], 'file', 'extensions' => 'png, jpg, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    public function getUseStartDate()
    {
        $use = UseYears::find()->where(['as_admin_id' => $this->id])->one();
        return $use->start_date;
    }

    public function getUseEndDate()
    {
        $use = UseYears::find()->where(['as_admin_id' => $this->id])->one();
        return $use->end_date;
    }

    public function GetNewId()
    {
        return AsAdmin::find()->orderBy('id DESC')->one()->id + 1;
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/as-admin/scan/';
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
        $path = '@app/upload/files/as-admin/license/';
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
                if ($asInstallOne->count !== "")
                    $asInstallOne->save();
            }
        if ($this->useStartDate == null && count($changedAttributes) > 0 && !(count($changedAttributes) == 1 && $changedAttributes['license_status'] !== null)) $this->useStartDate = '1999-01-01';
        if ($this->useEndDate == null && count($changedAttributes) > 0 && !(count($changedAttributes) == 1 && $changedAttributes['license_status'] !== null)) $this->useEndDate = '1999-01-01';

        if (count($changedAttributes) > 0 && !(count($changedAttributes) == 1 && $changedAttributes['license_status'] !== null))
        {
            $use = UseYears::find()->where(['as_admin_id' => $this->id])->one();
            if ($use === null)
                $use = new UseYears();
            $use->as_admin_id = $this->id;
            $use->start_date = $this->useStartDate;
            $use->end_date = $this->useEndDate;
            $use->save(false);
        }
    }

    public function beforeSave($insert)
    {
        $date = new DateTime(date("Y-m-d"));
        if ($this->getUseEndDate() !== $this->useEndDate && $this->useEndDate !== null)
        {
            if ($this->useEndDate > $date->format('Y-m-d') || $this->useEndDate == '1999-01-01')
                $this->license_status = 1;
            else
                $this->license_status = 0;
        }
        else
        {
            if (($this->getUseEndDate() > $date->format('Y-m-d') || $this->getUseEndDate() == '1999-01-01') && $this->getUseStartDate() < $date->format('Y-m-d') )
                $this->license_status = 1;
            else
                $this->license_status = 0;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
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
