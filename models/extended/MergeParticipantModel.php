<?php


namespace app\models\extended;



class MergeParticipantModel extends \yii\base\Model
{
    public $fio1;
    public $fio2;
    public $id1;
    public $id2;

    public $firstname;
    public $secondname;
    public $patronymic;
    public $sex;
    public $pd = [];

    public $target_id;

    public function rules()
    {
        return [
            [['firstname', 'secondname', 'patronymic', 'fio1', 'fio2'], 'string'],
            ['pd', 'safe'],
            [['sex', 'target_id', 'id1', 'id2'], 'integer'],
        ];
    }

    public function save()
    {
        $this->file->saveAs('@app/upload/files/bitrix/groups/' . $this->file->name);
        ExcelWizard::GetAllParticipants($this->file->name);
    }
}