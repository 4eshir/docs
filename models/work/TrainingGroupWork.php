<?php

namespace app\models\work;

use app\models\common\Auditorium;
use app\models\common\BranchProgram;
use app\models\common\ForeignEventParticipants;
use app\models\common\GroupErrors;
use app\models\common\LessonTheme;
use app\models\common\OrderGroup;
use app\models\common\People;
use app\models\common\TeacherGroup;
use app\models\common\ThematicPlan;
use app\models\common\TrainingGroup;
use app\models\common\TrainingGroupLesson;
use app\models\common\TrainingGroupParticipant;
use app\models\common\TrainingProgram;
use app\models\common\Visit;
use app\models\components\ExcelWizard;
use app\models\components\FileWizard;
use app\models\components\LessonDatesJob;
use Mpdf\Tag\Tr;
use Yii;
use yii\helpers\Html;
use yii\queue\db\Queue;


class TrainingGroupWork extends TrainingGroup
{

    public $photosFile;
    public $presentDataFile;
    public $workDataFile;

    public $certFile;

    public $participants;
    public $lessons;
    public $auto;
    public $orders;
    public $teachers;

    public $fileParticipants;

    public $delArr;

    public $branchId;

    public $participant_id;

    public $certificatArr = [];
    public $sendMethodArr = [];
    public $idArr = [];


    public function rules()
    {
        return [
            [['start_date', 'finish_date', 'budget'], 'required'],
            [['training_program_id', 'teacher_id', 'open', 'budget', 'branchId', 'participant_id', 'branch_id'], 'integer'],
            [['start_date', 'finish_date', 'schedule_type', 'certificatArr', 'sendMethodArr', 'idArr', 'delArr'], 'safe'],
            //[['delArr'], 'each', 'rule' => ['string']],
            [['photos', 'present_data', 'work_data', 'number'], 'string', 'max' => 1000],
            [['photosFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['certFile'], 'file', 'extensions' => 'xlsx, xls', 'skipOnEmpty' => true, 'maxSize' => 26214400],
            [['presentDataFile'], 'file', 'extensions' => 'jpg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['workDataFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['fileParticipants'], 'file', 'extensions' => 'xls, xlsx', 'maxSize' => 26214400, 'skipOnEmpty' => true],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleWork::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgramWork::className(), 'targetAttribute' => ['training_program_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Номер',
            'numberView' => 'Номер',
            'training_program_id' => 'Образовательная программа',
            'programName' => 'Образовательная программа',
            'programNameNoLink' => 'Образовательная программа',
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
            'open' => 'Перенести темы занятий из образовательной программы',
            'openText' => 'Расписание утверждено',
            'participantNames' => 'Состав',
            'lessonDates' => 'Расписание',
            'scheduleType' => 'Тип расписания',
            'ordersName' => 'Приказы',
            'certFile' => 'Файл сертификатов',
            'budget' => 'Бюджет',
            'fileParticipants' => 'Загрузить учащихся из файла',
            'teachersList' => 'Педагог(-и)',
            'branch_id' => 'Отдел производящий учёт',
            'order_status' => 'Статус добавления приказов',
            'order_stop' => 'Завершить загрузку приказов о зачислении/отчислении',
        ];
    }

    public function getTeacherWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'teacher_Id']);
    }

    public function getTeachersList()
    {
        $teachers = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->all();
        $result = "";
        foreach ($teachers as $teacher)
            $result .= Html::a($teacher->teacherWork->shortName, \yii\helpers\Url::to(['people/view', 'id' => $teacher->teacher_id])) . '<br>';
        return $result;
    }

    public function getNumberView()
    {
        return Html::a($this->number, \yii\helpers\Url::to(['training-group/view', 'id' => $this->id]));
    }

    public function getProgramName()
    {
        $prog = TrainingProgramWork::find()->where(['id' => $this->training_program_id])->one();
        return Html::a($prog->name, \yii\helpers\Url::to(['training-program/view', 'id' => $prog->id]));
    }

    public function getProgramNameNoLink()
    {
        $prog = TrainingProgramWork::find()->where(['id' => $this->training_program_id])->one();
        return $prog->name;
    }

    public function getOpenText()
    {
        return $this->open ? 'Да' : 'Нет';
    }

    public function getBudgetText()
    {
        return $this->budget ? 'Бюджет' : 'Внебюджет';
    }

    public function getJournalLink()
    {
        return Html::a('Журнал группы '.$this->number, \yii\helpers\Url::to(['journal/index', 'group_id' => $this->id]));
    }

    public function getBranchName()
    {
        return Html::a($this->branchWork, \yii\helpers\Url::to(['training-program/view', 'id' => $this->branch_id]));;
    }

    public function getParticipantNames()
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $result = '';
        foreach ($parts as $part)
        {
            $result .= Html::a($part->participantWork->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participant_id]));
            if ($part->status == 1)
                $result .= ' <font color=red><i>ОТЧИСЛЕН</i></font>';
            else
                $result .= ' Сертификат № ' . $part->certificat_number;
            $result .= '<br>';
        }
        return $result;
    }

    public function getCountParticipants()
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $result = count($parts) . ' (включая отчисленных)';
        return $result;
    }

    public function getCountLessons()
    {
        $parts = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $result = count($parts) . ' академ.часа';
        return $result;
    }

    public function getLessonDates()
    {

        //$parts = TrainingGroupLessonWork::findBySql('SELECT * FROM `training_group_lesson` WHERE `training_group_id` = '.$this->id.' ORDER BY `lesson_date` ASC')->all();
        $parts = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->orderBy(['lesson_date' => SORT_ASC, 'lesson_start_time' => SORT_ASC])->all();


        $result = '';
        $counter = 0;
        foreach ($parts as $part)
        {
            //ГДЕ ТО ЗДЕСЬ ПРОИСХОДИЛА КУЧА ЗАПРОСОВ ВИДА SELECT * FROM `training_group_lesson` WHERE id != /рандомное_число/
            //ВРЕМЯ ЧАС НОЧИ ТАК ЧТО Я ПРОСТО ЗАКОММЕНТИЛ ВСЕ И РАБОТАЕТ ТЕПЕРЬ БЫСТРО
            /*if ($part->lesson_date < $this->start_date)
                $result .= '<font style="color: indianred">'.date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->fullName.' <i>ОШИБКА: дата занятия раньше даты начала курса</i></font><br>';
            else if ($part->lesson_date > $this->finish_date)
                $result .= '<font style="color: indianred">'.date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->fullName.' <i>ОШИБКА: дата занятия позже даты окончания курса</i></font><br>';
            else if (count($part->checkValideTime($this->id)) > 0)
            {
                //$number = TrainingGroupLesson::find()->where(['id' => $part->checkValideTime($this->id)[0]])->one();
                $result .= '<font style="color: indianred">'.date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->name.' <i>ОШИБКА: на данное время назначено занятие у Группы №'.$number->trainingGroup->number.'</i></font><br>';
            }
            else*/
                $result .= date('d.m.Y', strtotime($part->lesson_date)).' с '.substr($part->lesson_start_time, 0, -3).' до '.substr($part->lesson_end_time, 0, -3).' в ауд. '.$part->auditorium->name.'<br>';
            $counter++;
        }
        return $result;

    }

    public function getOrdersName()
    {
        $orders = OrderGroupWork::find()->where(['training_group_id' => $this->id])->all();
        $result = '';
        foreach ($orders as $order)
        {
            $result .= Html::a($order->documentOrderWork->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->documentOrderWork->id])).'<br>';
        }
        return $result;
    }

    public function getBranchWork()
    {
        $branch =  BranchWork::find()->where(['id' => $this->branch_id])->one();
        $result = Html::a($branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $branch->id]));
        return $result;
    }

    public function getErrorsWork()
    {
        $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $this->id, 'time_the_end' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $error = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            $result .= 'Внимание, ошибка: ' . $error->number . ' ' . $error->name . '<br>';
        }
        return $result;
    }

    public function getManHoursPercent()
    {
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $lessonsId = [];
        foreach ($lessons as $lesson)
            $lessonsId[] = $lesson->id;
        $visits = count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['status' => 0])->all());
        $maximum = count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all()) * count(TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all());
        $percent = (($visits * 1.0) / ($maximum * 1.0)) * 100;
        $numbPercent = $percent;
        $percent = round($percent, 2);
        if ($numbPercent > 75.0)
            $percent = '<p style="color: #1e721e; display: inline">'.$percent;
        else if ($numbPercent > 50.0)
            $percent = '<p style="color: #d49939; display: inline">' .$percent;
        else
            $percent = '<p style="color: #c34444; display: inline">' .$percent;
            $percent = '<p style="color: #c34444; display: inline">' .$percent;
        $result = $visits.' / '.$maximum.' (<b>'.$percent.'%</b></p>)';
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

    public function uploadFileCert()
    {
        $this->certFile->saveAs('@app/upload/files/bitrix/groups/' . $this->certFile->name);
        ExcelWizard::WriteAllCertNumbers($this->certFile->name, $this->id);
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

    public function getTrainingProgramWork()
    {
        return $this->hasOne(TrainingProgramWork::className(), ['id' => 'training_program_id']);
    }

    public function GenerateNumber()
    {
        $teacher = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->orderBy(['id' => SORT_ASC])->one()->teacher_id;
        $level = $this->trainingProgramWork->level;
        $level++;
        $this->number = $this->trainingProgramWork->thematicDirection->name.'.'.$level.'.'.PeopleWork::find()->where(['id' => $teacher])->one()->short.'.'.str_replace('-', '', $this->start_date);
        $counter = count(TrainingGroupWork::find()->where(['like', 'number', $this->number.'%', false])->andWhere(['!=', 'id', $this->id])->all());
        //$current = TrainingGroupWork::find()->where(['id' => $this->id])->one();
        $counter++;
        for($index = 1; $index <= $counter; $index++)
        {
            $twin = TrainingGroupWork::find()->where(['like', 'number', $this->number.'.'.$index, false])->andWhere(['!=', 'id', $this->id])->all();
            if ($twin == null)
            {
                $this->number .= '.' . $index;
                $index = $counter;
            }
        }

        /*if ($current !== null)
        {
            if (!is_numeric(substr($current->number, -1)))
                $this->number .= '.'.$counter;
            else
                $this->number = $current->number;
        }
        else
            $this->number .= '.'.$counter;*/
    }

    public function cmp($a, $b)
    {
        if ($a["participant_id"] == $b["participant_id"]) return 0;
        return ($a["participant_id"] < $b["participant_id"]) ? -1 : 1;
    }

    public function afterSave($insert, $changedAttributes)
    {

        if (!(count($changedAttributes) === 0 || $changedAttributes["archive"] !== null))
        {
            parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
            if (array_key_exists('deleteChoose', $_POST) && $this->delArr !== null) {
                $newDel = [];
                foreach ($this->delArr as $oneDel) {
                    if ($oneDel !== "0") {
                        $newDel[] = $oneDel;
                    }
                }

                $lessCount = count(TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all());
                if (count($newDel) > $lessCount)
                {
                    $counter = 0;
                    while ($counter < $lessCount)
                    {
                        unset($newDel[count($newDel) - 1]);
                        $counter++;
                    }
                }


                foreach ($newDel as $oneDel) {
                    $lesson = TrainingGroupLessonWork::find()->where(['id' => $oneDel])->one();
                    $themes = LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->all();
                    $visits = VisitWork::find()->where(['training_group_lesson_id' => $lesson->id])->all();

                    foreach ($themes as $theme) $theme->delete();
                    foreach ($visits as $visit) $visit->delete();
                    if ($lesson !== null)
                       $lesson->delete();
                }
                /*
                            $extEvents = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->orderBy(['lesson_date' => SORT_ASC, 'lesson_start_time' => SORT_ASC])->all();
                            $newArr = [];
                            $idsArr = [];
                            $index = 0;
                            while (count($newArr) < count($extEvents))
                            {
                                if ($this->delArr[$index] == 0 && $this->delArr[$index + 1] == 0)
                                {
                                    $newArr[] = 0;
                                    $index += 2;
                                }
                                else if ($this->delArr[$index] == 0)
                                {
                                    $newArr[] = 1;
                                    $index += 2;
                                }
                                else
                                {
                                    $newArr[] = 1;
                                    $index += 1;
                                }
                            }

                            for ($i = 0; $i < count($newArr); $i++)
                                if ($newArr[$i] === 1)
                                    $idsArr[] = $extEvents[$i]->id;

                            for ($i = 0; $i < count($idsArr); $i++)
                            {

                                $themes = LessonThemeWork::find()->where(['training_group_lesson_id' => $idsArr[$i]])->all();
                                $visits = Visit::find()->where(['training_group_lesson_id' => $idsArr[$i]])->all();
                                //foreach ($themes as $theme) $theme->delete();
                                //foreach ($visits as $visit) $visit->delete();
                                $event = TrainingGroupLessonWork::find()->where(['id' => $idsArr[$i]])->one();

                                //$event->delete();

                            }
                */
            }

            $partsArr = [];
            $errArr = [];
            if ($this->participants !== null && $this->participants[0]->participant_id !== "") {
                usort($this->participants, array($this, "cmp"));
                $tempParts = [];
                $copyMessage = "";
                if ($this->checkOldParticipant($this->participants[0]->participant_id))
                    $tempParts[] = $this->participants[0];
                else
                    $copyMessage .= $this->participants[0]->participantWork->shortName.'<br>';
                for ($i = 1; $i < count($this->participants); $i++)
                {
                    if ($this->participants[$i]->participant_id !== $this->participants[$i - 1]->participant_id && $this->checkOldParticipant($this->participants[$i]->participant_id))
                        $tempParts[] = $this->participants[$i];
                    else
                        $copyMessage .= $this->participants[$i]->participantWork->shortName.'<br>';
                }
                $this->participants = $tempParts;
                foreach ($this->participants as $participant) {
                    if ($participant->participant_id !== "")
                    {
                        $trainingParticipant = new TrainingGroupParticipant();
                        $trainingParticipant->participant_id = $participant->participant_id;
                        $trainingParticipant->certificat_number = $participant->certificat_number;
                        $trainingParticipant->send_method_id = $participant->send_method_id;
                        $trainingParticipant->training_group_id = $this->id;
                        $trainingParticipant->save();
                        $partsArr[] = $trainingParticipant->participant_id;
                    }
                    else
                    {
                        $errArr[] = $participant->participant_name;
                    }
                }

                if (count($errArr) > 0)
                {
                    $message = "Следующие обучающиеся не были найдены в базе:<br>";
                    foreach ($errArr as $errOne)
                        $message .= $errOne.'<br>';
                    $message .= "<br>Для добавления обучающихся в базу, обратитесь к методисту";
                    Yii::$app->session->setFlash("danger", $message);
                }
                if (strlen($copyMessage) > 3)
                {
                    $copyMessage = "При загрузке были обнаружены дубликаты обучаюшихся: <br>" . $copyMessage;
                    Yii::$app->session->setFlash("warning", $copyMessage);
                }
            }
            if ($this->lessons[0]->lesson_date !== null && $this->lessons[0]->lesson_date !== "") {
                foreach ($this->lessons as $lesson) {
                    $newLesson = new TrainingGroupLessonWork();
                    $newLesson->lesson_date = $lesson->lesson_date;
                    $newLesson->lesson_start_time = $lesson->lesson_start_time;
                    $min = $this->trainingProgram->hour_capacity;
                    $newLesson->lesson_end_time = date("H:i", strtotime('+' . $min . ' minutes', strtotime($lesson->lesson_start_time)));
                    $newLesson->duration = $this->trainingProgram->hour_capacity;
                    $aud = Auditorium::find()->where(['id' => $lesson->auds])->one();
                    $newLesson->branch_id = $lesson->auditorium_id;
                    $newLesson->auditorium_id = $aud->id;
                    $newLesson->training_group_id = $this->id;
                    if ($newLesson->checkCopyLesson())
                        $newLesson->save(false);
                }
            }
            if ($this->auto[0]->day !== null && $this->auto[0]->day !== '') {
                foreach ($this->auto as $autoOne) {
                    $days = $autoOne->getDaysInRange($this->start_date, $this->finish_date);
                    foreach ($days as $day) {
                        $newLesson = new TrainingGroupLessonWork();
                        $newLesson->lesson_date = $day;
                        $newLesson->lesson_start_time = $autoOne->start_time;
                        $min = $this->trainingProgram->hour_capacity;
                        $newLesson->lesson_end_time = date("H:i", strtotime('+' . $min . ' minutes', strtotime($autoOne->start_time)));
                        $newLesson->duration = $this->trainingProgram->hour_capacity;
                        $aud = Auditorium::find()->where(['id' => $autoOne->auds])->one();
                        $newLesson->branch_id = $autoOne->auditorium_id;
                        $newLesson->auditorium_id = $aud->id;
                        $newLesson->training_group_id = $this->id;
                        if ($newLesson->checkCopyLesson())
                            $newLesson->save(false);
                    }
                }
            }

            if ($this->orders !== null && $this->orders[0]->document_order_id !== '') {
                foreach ($this->orders as $order) {
                    $newOrder = new OrderGroup();
                    $newOrder->document_order_id = $order->document_order_id;
                    $newOrder->training_group_id = $this->id;
                    $newOrder->comment = $order->comment;
                    $newOrder->save();
                }
            }
            if ($this->teachers !== null && $this->teachers[0]->teacher_id !== "") {
                foreach ($this->teachers as $teacher) {
                    $teacherGroup = TeacherGroup::find()->where(['teacher_id' => $this->teachers[0]->teacher_id])->andWhere(['training_group_id' => $this->id])->one();
                    if ($teacherGroup === null)
                        $teacherGroup = new TeacherGroup();
                    $teacherGroup->teacher_id = $teacher->teacher_id;
                    $teacherGroup->training_group_id = $this->id;
                    $teacherGroup->save();
                }
            }


            $participants = TrainingGroupParticipant::find()->where(['training_group_id' => $this->id])->all();
            $participantsId = [];
            foreach ($participants as $pOne)
                $participantsId[] = $pOne->participant_id;

            $lessons = TrainingGroupLesson::find()->where(['training_group_id' => $this->id])->all();
            $lessonsId = [];
            foreach ($lessons as $lOne)
                $lessonsId[] = $lOne->id;

            foreach ($lessonsId as $lId) {
                foreach ($participantsId as $pId) {
                    $visit = Visit::find()->where(['foreign_event_participant_id' => $pId])->andWhere(['training_group_lesson_id' => $lId])->one();
                    if ($visit === null) {
                        $visit = new Visit();
                        $visit->foreign_event_participant_id = $pId;
                        $visit->training_group_lesson_id = $lId;
                        $visit->status = 3;
                        $visit->save(false);
                    }
                }
            }

            if ($this->open === 1) {

                $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();
                $tp = ThematicPlanWork::find()->where(['training_program_id' => $this->training_program_id])->orderBy(['id' => SORT_ASC])->all();
                $teachers = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->all();
                $counter = 0;
                for ($i = 0; $i < count($lessons); $i++) {
                    $theme = LessonThemeWork::find()->where(['training_group_lesson_id' => $lessons[$i]->id])->andWhere(['teacher_id' => $teachers[0]->teacher_id])->one();
                    if ($theme !== null) $counter++;
                }

                if (count($lessons) === count($tp) && $counter == 0) {
                    for ($i = 0; $i < count($tp); $i++) {
                        $theme = LessonThemeWork::find()->where(['training_group_lesson_id' => $lessons[$i]->id])->andWhere(['teacher_id' => $teachers[0]->teacher_id])->one();
                        if ($theme === null)
                            $theme = new LessonThemeWork();
                        $theme->theme = $tp[$i]->theme;
                        $theme->training_group_lesson_id = $lessons[$i]->id;
                        $theme->teacher_id = $teachers[0]->teacher_id;
                        $theme->save(false);
                    }
                }
            }

            //блок сохранения сертификатов через внутреннюю подформу
            for ($i = 0; $i < count($this->idArr); $i++) {
                $cert = TrainingGroupParticipantWork::find()->where(['id' => $this->idArr[$i]])->one();
                if ($this->sendMethodArr[$i] !== null && strlen($this->certificatArr[$i]) > 0) {
                    $cert->send_method_id = $this->sendMethodArr[$i];
                    $cert->certificat_number = $this->certificatArr[$i];
                    $cert->save();
                }
            }

            // тут должны работать проверки на ошибки
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckErrorsTrainingGroup($this->id);
        }
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $teachers = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->all();
        $orders = OrderGroup::find()->where(['training_group_id' => $this->id])->all();
        $visits = Visit::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $this->id])->all();
        foreach ($visits as $visit) $visit->delete();
        foreach ($teachers as $teacher) $teacher->delete();
        foreach ($lessons as $lesson)
        {
            $themes = LessonTheme::find()->where(['training_group_lesson_id' => $lesson->id])->all();
            foreach ($themes as $theme)
                $theme->delete();
            $lesson->delete();
        }
        foreach ($parts as $part) $part->delete();
        foreach ($orders as $order) $order->delete();

        $errors = GroupErrorsWork::find()->where(['training_group_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function checkOldParticipant($participant_id)
    {
        $allParts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        foreach ($allParts as $part)
            if ($part->participant_id == $participant_id)
                return false;
        return true;
    }
}
