<?php

namespace app\models\common;

use DateTime;
use Yii;


class AsAdminWork extends AsAdmin
{
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