<?php

namespace app\models\common;

use app\models\components\ExcelWizard;
use app\models\components\FileWizard;
use Mpdf\Tag\Tr;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "training_group".
 *
 * @property int $id
 * @property string $number
 * @property int|null $training_program_id
 * @property int $teacher_id
 * @property string $start_date
 * @property string $finish_date
 * @property string|null $photos
 * @property string|null $present_data
 * @property string|null $work_data
 * @property int $open
 * @property int $schedule_type
 * @property int $budget
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
    public $lessons;
    public $auto;
    public $orders;
    public $teachers;

    public $fileParticipants;

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
            [['number', 'teacher_id', 'start_date', 'finish_date', 'budget'], 'required'],
            [['training_program_id', 'teacher_id', 'open', 'budget'], 'integer'],
            [['start_date', 'finish_date', 'schedule_type'], 'safe'],
            [['photos', 'present_data', 'work_data', 'number'], 'string', 'max' => 1000],
            [['photosFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['presentDataFile'], 'file', 'extensions' => 'jpg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['workDataFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['fileParticipants'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
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
            'participantNames' => 'Состав',
            'lessonDates' => 'Расписание',
            'scheduleType' => 'Тип расписания',
            'ordersName' => 'Приказы',
            'budget' => 'Бюджет',
            'fileParticipants' => 'Загрузить учащихся из файла',
            'teachersList' => 'Педагог(-и)',
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
    public function getTeachersList()
    {
        $teachers = TeacherGroup::find()->where(['training_group_id' => $this->id])->all();
        $result = "";
        foreach ($teachers as $teacher)
            $result .= Html::a($teacher->teacher->shortName, \yii\helpers\Url::to(['people/view', 'id' => $teacher->teacher_id])) . '<br>';
        return $result;
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

    public function getParticipantNames()
    {
        $parts = TrainingGroupParticipant::find()->where(['training_group_id' => $this->id])->all();
        $result = '';
        foreach ($parts as $part)
        {
            $result .= Html::a($part->participant->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participant_id]));
            if ($part->status == 1)
                $result .= ' <font color=red><i>ОТЧИСЛЕН</i></font>';
            $result .= '<br>';
        }
        return $result;
    }

    public function getLessonDates()
    {
        $parts = TrainingGroupLesson::find()->where(['training_group_id' => $this->id])->orderBy(['lesson_date' => SORT_ASC])->all();
        $result = '';
        foreach ($parts as $part)
        {
            if ($part->lesson_date < $this->start_date)
                $result .= '<font style="color: indianred">'.date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->fullName.' <i>ОШИБКА: дата занятия раньше даты начала курса</i></font><br>';
            else if ($part->lesson_date > $this->finish_date)
                $result .= '<font style="color: indianred">'.date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->fullName.' <i>ОШИБКА: дата занятия позже даты окончания курса</i></font><br>';
            else if (count($part->checkValideTime($this->id)) > 0)
            {
                $number = TrainingGroupLesson::find()->where(['id' => $part->checkValideTime($this->id)[0]])->one();
                $result .= '<font style="color: indianred">'.date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->name.' <i>ОШИБКА: на данное время назначено занятие у Группы №'.$number->trainingGroup->number.'</i></font><br>';
            }
            else
                $result .= date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->name.'<br>';
        }
        return $result;
    }

    public function getOrdersName()
    {
        $orders = OrderGroup::find()->where(['training_group_id' => $this->id])->all();
        $result = '';
        foreach ($orders as $order)
        {
            $result .= Html::a($order->documentOrder->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->documentOrder->id])).'<br>';
        }
        return $result;
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

    public function uploadFileParticipants()
    {
        $this->fileParticipants->saveAs('@app/upload/files/bitrix/groups/' . $this->fileParticipants->name);
        $parts = ExcelWizard::GetAllParticipants($this->fileParticipants->name);
        $this->addParticipants($parts);
    }

    private function addParticipants($participants)
    {

        if ($participants !== null && count($participants) > 0)
        {
            for ($i = 0; $i !== count($participants); $i++)
            {
                $newTrainingGroupParticipant = TrainingGroupParticipant::find()->where(['participant_id' => $participants[$i]->id])->andWhere(['training_group_id' => $this->id])->one();
                if ($newTrainingGroupParticipant == null)
                {
                    $newTrainingGroupParticipant = new TrainingGroupParticipant();
                    $newTrainingGroupParticipant->participant_id = $participants[$i]->id;
                    $newTrainingGroupParticipant->training_group_id = $this->id;
                    $newTrainingGroupParticipant->save();
                }
            }
        }
    }

    public function GenerateNumber()
    {
        $teacher = TeacherGroup::find()->where(['training_group_id' => $this->id])->orderBy(['id' => SORT_ASC])->one()->teacher_id;
        $level = $this->trainingProgram->level;
        $level++;
        $this->number = $this->trainingProgram->thematicDirection->name.'.'.$level.'.'.People::find()->where(['id' => $teacher])->one()->short.'.'.str_replace('-', '', $this->start_date);
        $counter = count(TrainingGroup::find()->where(['like', 'number', $this->number.'%', false])->andWhere(['!=', 'id', $this->id])->all());
        $current = TrainingGroup::find()->where(['id' => $this->id])->one();
        $counter++;
        if ($current !== null)
        {
            if (!is_numeric(substr($current->number, -1)))
                $this->number .= '.'.$counter;
            else
                $this->number = $current->number;
        }
        else
            $this->number .= '.'.$counter;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $partsArr = [];
        if ($this->participants !== null && $this->participants[0]->participant_id !== "")
        {
            foreach ($this->participants as $participant)
            {
                $trainingParticipant = new TrainingGroupParticipant();
                $trainingParticipant->participant_id = $participant->participant_id;
                $trainingParticipant->certificat_number = $participant->certificat_number;
                $trainingParticipant->send_method_id = $participant->send_method_id;
                $trainingParticipant->training_group_id = $this->id;
                $trainingParticipant->save();
                $partsArr[] = $trainingParticipant->participant_id;
            }
        }
        if ($this->lessons[0]->lesson_date !== null && $this->lessons[0]->lesson_date !== "")
        {
            foreach ($this->lessons as $lesson)
            {
                $newLesson = new TrainingGroupLesson();
                $newLesson->lesson_date = $lesson->lesson_date;
                $newLesson->lesson_start_time = $lesson->lesson_start_time;
                $min = $this->trainingProgram->hour_capacity;
                $newLesson->lesson_end_time = date("H:i", strtotime('+'.$min.' minutes', strtotime($lesson->lesson_start_time)));
                $newLesson->duration = $lesson->duration;
                $aud = Auditorium::find()->where(['id' => $lesson->auds])->one();
                $newLesson->branch_id = $lesson->auditorium_id;
                $newLesson->auditorium_id = $aud->id;
                $newLesson->training_group_id = $this->id;
                $newLesson->save();
            }
        }
        if ($this->auto[0]->day !== null && $this->auto[0]->day !== '')
        {
            foreach ($this->auto as $autoOne)
            {
                $days = $autoOne->getDaysInRange($this->start_date, $this->finish_date);
                foreach ($days as $day)
                {
                    $newLesson = new TrainingGroupLesson();
                    $newLesson->lesson_date = $day;
                    $newLesson->lesson_start_time = $autoOne->start_time;
                    $min = $this->trainingProgram->hour_capacity;
                    $newLesson->lesson_end_time = date("H:i", strtotime('+'.$min.' minutes', strtotime($autoOne->start_time)));
                    $newLesson->duration = $autoOne->duration;
                    $aud = Auditorium::find()->where(['id' => $autoOne->auds])->one();
                    $newLesson->branch_id = $autoOne->auditorium_id;
                    $newLesson->auditorium_id = $aud->id;
                    $newLesson->training_group_id = $this->id;
                    $newLesson->save();
                }
            }
        }

        if ($this->orders !== null && $this->orders[0]->document_order_id !== '')
        {
            foreach ($this->orders as $order)
            {
                $newOrder = new OrderGroup();
                $newOrder->document_order_id = $order->document_order_id;
                $newOrder->training_group_id = $this->id;
                $newOrder->comment = $order->comment;
                $newOrder->save();
            }
        }
        if ($this->teachers !== null && $this->teachers[0]->teacher_id !== "")
        {
            foreach ($this->teachers as $teacher)
            {
                $teacherGroup = new TeacherGroup();
                $teacherGroup->teacher_id = $teacher->teacher_id;
                $teacherGroup->training_group_id = $this->id;
                $teacherGroup->save();
            }
        }


        /*if (count($partsArr) > 0)
        {
            foreach ($partsArr as $participant)
            {
                $lessonsArr = TrainingGroupLesson::find()->where(['training_group_id' => $this->id])->all();
                foreach ($lessonsArr as $lesson)
                {
                    $visit = new Visit();
                    $visit->foreign_event_participant_id = $participant;
                    $visit->training_group_lesson_id = $lesson->id;
                    $visit->status = 3;
                    $visit->save(false);
                }
            }
        }*/

        $participants = TrainingGroupParticipant::find()->where(['training_group_id' => $this->id])->all();
        $participantsId = [];
        foreach ($participants as $pOne)
            $participantsId[] = $pOne->participant_id;

        $lessons = TrainingGroupLesson::find()->where(['training_group_id' => $this->id])->all();
        $lessonsId = [];
        foreach ($lessons as $lOne)
            $lessonsId[] = $lOne->id;

        foreach ($lessonsId as $lId)
        {
            foreach ($participantsId as $pId)
            {
                $visit = Visit::find()->where(['foreign_event_participant_id' => $pId])->andWhere(['training_group_lesson_id' => $lId])->one();
                if ($visit === null)
                {
                    $visit = new Visit();
                    $visit->foreign_event_participant_id = $pId;
                    $visit->training_group_lesson_id = $lId;
                    $visit->status = 3;
                    $visit->save(false);
                }
            }
        }


        if ($this->open === 1)
        {

            $lessons = TrainingGroupLesson::find()->where(['training_group_id' => $this->id])->all();
            $tp = ThematicPlan::find()->where(['training_program_id' => $this->training_program_id])->orderBy(['id' => SORT_ASC])->all();
            $teachers = TeacherGroup::find()->where(['training_group_id' => $this->id])->all();

            if (count($lessons) === count($tp))
            {
                for ($i = 0; $i < count($tp); $i++)
                {
                    $theme = new LessonTheme();
                    $theme->theme = $tp[$i]->theme;
                    $theme->training_group_lesson_id = $lessons[$i]->id;
                    $theme->teacher_id = $teachers[0]->teacher_id;
                    $theme->save();
                }
            }
        }

    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        $parts = TrainingGroupParticipant::find()->where(['training_group_id' => $this->id])->all();
        $lessons = TrainingGroupLesson::find()->where(['training_group_id' => $this->id])->all();
        $teachers = TeacherGroup::find()->where(['training_group_id' => $this->id])->all();
        $visits = Visit::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $this->id])->all();
        foreach ($visits as $visit) $visit->delete();
        foreach ($teachers as $teacher) $teacher->delete();
        foreach ($lessons as $lesson) $lesson->delete();
        foreach ($parts as $part) $part->delete();

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}
