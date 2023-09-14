<?php


namespace app\models\extended;


use app\models\common\ForeignEventParticipants;
use app\models\components\FileWizard;
use yii\base\Model;

class ForeignEventParticipantsExtended extends Model
{

    public $fio;
    public $teacher;
    public $teacher2;
    public $branch;
    public $focus;
    public $file;
    public $team;
    public $fileString;

    public $allow_remote_id;
    public $nomination;

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400],
            [['teacher', 'teacher2', 'fileString', 'focus', 'team', 'nomination'], 'string'],
            [['fio', 'branch', 'allow_remote_id'], 'integer'],
        ];
    }

    public function uploadFile($event_name, $event_date)
    {
        $path = '@app/upload/files/foreign-event/participants/';
        $date = $event_date;
        $new_date = '';
        $filename = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $participant = ForeignEventParticipants::find()->where(['id' => $this->fio])->one();
        $filename = $participant->secondname.'_'.$new_date.'_'.$event_name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^a-zA-Zа-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->fileString = $res.'.'.$this->file->extension;
        $this->file->saveAs( $path.$this->fileString);
    }

    public function uploadCopyFile($filename)
    {
        $this->fileString = $filename;
    }
}