<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "training_group".
 *
 * @property int $id
 * @property int $number
 * @property int|null $training_program_id
 * @property int $teacher_id
 * @property string $start_date
 * @property string $finish_date
 * @property string|null $photos
 * @property string|null $present_data
 * @property string|null $work_data
 * @property int $open
 *
 * @property People $teacher
 * @property TrainingProgram $trainingProgram
 * @property TrainingProgramParticipant[] $trainingProgramParticipants
 */
class TrainingGroup extends \yii\db\ActiveRecord
{
    public $photosFile;
    public $presentDataFile;
    public $workDataFile;

    public $participants;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'teacher_id', 'start_date', 'finish_date'], 'required'],
            [['number', 'training_program_id', 'teacher_id', 'open'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['photos', 'present_data', 'work_data'], 'string', 'max' => 1000],
            [['photosFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['presentDataFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['workDataFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::className(), 'targetAttribute' => ['training_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Номер',
            'training_program_id' => 'Образовательная программа',
            'programName' => 'Образовательная программа',
            'teacher_id' => 'Педагог',
            'teacherName' => 'Педагог',
            'start_date' => 'Дата начала занятий',
            'finish_date' => 'Дата окончания занятий',
            'photos' => 'Фотоматериалы',
            'photosFile' => 'Фотоматериалы',
            'present_data' => 'Презентационные материалы',
            'presentDataFile' => 'Презентационные материалы',
            'work_data' => 'Рабочие материалы',
            'workDataFile' => 'Рабочие материалы',
            'open' => 'Утвердить расписание',
            'openText' => 'Расписание утверждено',
        ];
    }

    /**
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher_Id']);
    }

    /**
     * Gets query for [[TrainingProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgram()
    {
        return $this->hasOne(TrainingProgram::className(), ['id' => 'training_program_id']);
    }

    /**
     * Gets query for [[TrainingProgramParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgramParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::className(), ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingProgramParticipants]].
     *
     * @return string
     */
    public function getTeacherName()
    {
        $teacher = People::find()->where(['id' => $this->teacher_id])->one();
        return $teacher->shortName;
    }

    public function getProgramName()
    {
        $prog = TrainingProgram::find()->where(['id' => $this->training_program_id])->one();
        return Html::a($prog->name, \yii\helpers\Url::to(['training-program/view', 'id' => $prog->id]));
    }

    public function getOpenText()
    {
        return $this->open ? 'Да' : 'Нет';
    }

    public function uploadPhotosFile($upd = null)
    {
        $path = '@app/upload/files/group/photos/';
        $result = '';
        $counter = 0;
        if (strlen($this->photos) > 3)
            $counter = count(explode(" ", $this->photos)) - 1;
        foreach ($this->photosFile as $file) {
            $counter++;
            $date = $this->start_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            $filename = 'Фото'.$counter.'_'.$new_date.'_'.$this->number;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->photos = $result;
        else
            $this->photos = $this->photos.$result;
        return true;
    }

    public function uploadPresentDataFile($upd = null)
    {
        $path = '@app/upload/files/group/present_data/';
        $result = '';
        $counter = 0;
        if (strlen($this->present_data) > 3)
            $counter = count(explode(" ", $this->present_data)) - 1;
        foreach ($this->presentDataFile as $file) {
            $counter++;
            $date = $this->start_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            $filename = 'През'.$counter.'_'.$new_date.'_'.$this->number;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->present_data = $result;
        else
            $this->present_data = $this->present_data.$result;
        return true;
    }

    public function uploadWorkDataFile($upd = null)
    {
        $path = '@app/upload/files/group/work_data/';
        $result = '';
        $counter = 0;
        if (strlen($this->work_data) > 3)
            $counter = count(explode(" ", $this->work_data)) - 1;
        foreach ($this->workDataFile as $file) {
            $counter++;
            $date = $this->start_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            $filename = 'Раб'.$counter.'_'.$new_date.'_'.$this->number;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->work_data = $result;
        else
            $this->work_data = $this->work_data.$result;
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($this->participants !== null)
        {
            foreach ($this->participants as $participant)
            {
                $trainingParticipant = new TrainingGroupParticipant();
                $trainingParticipant->participant_id = $participant->participant_id;
                $trainingParticipant->certificat_number = $participant->certificat_number;
                $trainingParticipant->send_method_id = $participant->send_method_id;
                $trainingParticipant->training_group_id = $this->id;
                $trainingParticipant->save();
            }
        }
    }
}