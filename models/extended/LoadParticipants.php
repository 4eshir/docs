<?php


namespace app\models\extended;


use app\models\components\ExcelWizard;

class LoadParticipants extends \yii\base\Model
{
    public $filename;
    public $file;

    public function rules()
    {
        return [
            ['filename', 'string'],
            [['file'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    public function save()
    {
        $this->file->saveAs('@app/upload/files/bitrix/groups/' . $this->file->name);
        ExcelWizard::GetAllParticipants($this->file->name);
    }
}