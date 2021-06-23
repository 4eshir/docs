<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;


class AuditoriumWork extends Auditorium
{
    public $filesList;


    public function rules()
    {
        return [
            [['name', 'square', 'branch_id'], 'required'],
            [['square'], 'number'],
            [['is_education', 'branch_id', 'capacity'], 'integer'],
            [['name', 'text', 'files'], 'string', 'max' => 1000],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }


    public function GetIsEducation()
    {
        return $this->is_education ? 'Да' : 'Нет';
    }

    public function GetBranchName()
    {
        return Html::a($this->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $this->branch_id]));
    }

    public function uploadFiles($upd = null)
    {
        $path = '@app/upload/files/auds/';
        $result = '';
        $counter = 0;
        if (strlen($this->files) > 3)
            $counter = count(explode(" ", $this->files)) - 1;
        foreach ($this->filesList as $file) {
            $counter++;
            $filename = 'Файл'.$counter.'_'.$this->name.'_'.$this->id;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->files = $result;
        else
            $this->files = $this->files.$result;
        return true;
    }
}
