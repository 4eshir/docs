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
use app\models\components\RoleBaseAccess;
use app\models\null\PeopleNull;
use app\models\null\TrainingProgramNull;
use app\models\work\PeopleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingProgramWork;
use app\models\work\TrainingGroupExpertWork;
use app\models\work\GroupProjectThemesWork;
use app\models\work\UserWork;
use Mpdf\Tag\Tr;
use Yii;
use yii\helpers\Html;
use yii\queue\db\Queue;
use app\models\common;
use app\models\components\Logger;


const _MAX_FILE_SIZE = 26214400;

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
    public $themes;
    public $experts;

    public $fileParticipants;

    public $delArr;

    public $branchId;

    public $participant_id;

    public $certificatArr = [];
    public $sendMethodArr = [];
    public $idArr = [];

    //
    public function rules()
    {
        return [
            [['start_date', 'finish_date', 'budget'], 'required'],
            [['training_program_id', 'teacher_id', 'open', 'budget', 'branchId', 'participant_id', 'branch_id', 'order_stop', 'creator_id', 'last_edit_id', 'protection_confirm', 'is_network'], 'integer'],
            [['start_date', 'finish_date', 'protection_date', 'schedule_type', 'certificatArr', 'sendMethodArr', 'idArr', 'delArr'], 'safe'],
            //[['delArr'], 'each', 'rule' => ['string']],
            [['photos', 'present_data', 'work_data', 'number', 'creatorString'], 'string', 'max' => 1000],
            [['photosFile'], 'file', 'extensions' => 'jpg, jpeg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['certFile'], 'file', 'extensions' => 'xlsx, xls', 'skipOnEmpty' => true, 'maxSize' => 26214400],
            [['presentDataFile'], 'file', 'extensions' => 'jpg, jpeg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['workDataFile'], 'file', 'extensions' => 'jpg, jpeg, png, pdf, doc, docx, zip, rar, 7z, tag', 'maxSize' => 524288000, 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['fileParticipants'], 'file', 'extensions' => 'xls, xlsx', 'maxSize' => 26214400, 'skipOnEmpty' => true],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleWork::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserWork::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserWork::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'protection_date' => 'Дата выдачи сертифкатов',
            'protection_confirm' => 'Учебная группа допущена к итоговому контролю',
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
            'creatorString' => 'Создатель группы',
            'editorString' => 'Последний редактор',
            'expertsString' => 'Эксперты',
            'projectThemes' => 'Темы проектов',
            'is_network' => 'Сетевая форма обучения',
            'isNetwork' => 'Сетевая форма обучения',
        ];
    }

    public function getTeachersArray()
    {
        $teachers = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->all();
        return $teachers;
    }

    public function getNumberExtended()
    {
        $teachersStr = '';
        $teachers = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->all();
        foreach ($teachers as $teacher)
            $teachersStr .= $teacher->teacherWork->shortName.' ';

        return $this->number.' ('.$teachersStr.$this->trainingProgram->name.')';
    }

    public function getExpertsArray()
    {
        $experts = TrainingGroupExpertWork::find()->where(['training_group_id' => $this->id])->all();
        return $experts;
    }

    public function getExpertsString()
    {
        $exps = TrainingGroupExpertWork::find()->where(['training_group_id' => $this->id])->all();
        $res = '<table>';

        foreach ($exps as $exp)
        {
            $color = $exp->expertType->name == 'Внутренний' ? '#f0ad4e' : 'green';
            $res .= '<tr><td style="color: '.$color.'; padding-right: 10px"><b>'.$exp->expertType->name.'</b></td><td>'.Html::a($exp->expertWork->fullNameWithCompany, \yii\helpers\Url::to(['people/view', 'id' => $exp->expert_id])).'</td></tr>';
        }

        return $res.'</table>';
    }

    public function getProjectThemes()
    {
        $themes = GroupProjectThemesWork::find()->where(['training_group_id' => $this->id])->all();
        $res = '';

        foreach ($themes as $theme)
        {
            $res .= $theme->projectTheme->name.' ('.$theme->projectType->name.' проект)<br>';
        }

        return $res;
    }

    public function getTeacherWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'teacher_id']);
        return $try->all() ? $try : new PeopleNull();
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

    public function getIsNetwork()
    {
        return $this->is_network ? 'Да' : 'Нет';
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
        return Html::a($this->branchWork, \yii\helpers\Url::to(['training-program/view', 'id' => $this->branch_id]));
    }

    public function getVisitsError()
    {
        $visits = Visit::find()->joinWith(['trainingGroupLesson lesson'])->where(['lesson.training_group_id' => $this->id])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        return count($visits) !== count($lessons) * count($parts);
    }

    /*
     * return
     * 0 - Учится в группе
     * 1 - Отчислен
     * 2 - Переведен
     */
    public function CheckParticipantStatus($tgp)
    {
        $ordersG = OrderGroupWork::find()->where(['training_group_id' => $tgp->training_group_id])->orderBy(['id' => SORT_DESC])->all();


        foreach ($ordersG as $orderG) {
            $nom = NomenclatureWork::find()->where(['number' => $orderG->documentOrderWork->order_number])->andWhere(['actuality' => 0])->one();
            //проверка на отчисление
            if ($nom->type == 1)
                if (OrderGroupParticipantWork::find()->where(['group_participant_id' => $tgp->id])->andWhere(['order_group_id' => $orderG->id])->one() !== null)
                    return 1;

            //проверка на перевод
            if ($nom->type == 2)
                if (OrderGroupParticipantWork::find()->where(['group_participant_id' => $tgp->id])->andWhere(['order_group_id' => $orderG->id])->one() !== null)
                    return 2;
        }

        return 0;
    }
    //..
    public function getParticipantNames()
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $orders = OrderGroupParticipantWork::find()->joinWith(['groupParticipant group_participant']);
        $result = '';
        foreach ($parts as $part)
        {
            $pdDatabase = PersonalDataForeignEventParticipantWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->where(['foreignEventParticipant.id' => $part->participant_id])->andWhere(['personal_data_foreign_event_participant.status' => 1])->all();
            if (count($pdDatabase) > 0)
            {
                $text = 'Запрещено разглашение следующих ПД:&#10;';
                $i = 1;
                foreach ($pdDatabase as $one) {
                    $text .= $i.'. '.$one->personalData->name.'&#10;';
                    $i++;
                }
                $result .= '<div class="hoverless" data-html="true" id="tooltip'.$part->participant_id.'" style="width: 20px; height: 20px; padding: 0; margin-right: 5px; margin-top: 2px; background: #fd5e53; color: white; text-align: center; display: inline-block; border-radius: 4px" title="'.$text.'">!</div>';
            }
            else
                $result .= '<div class="hoverless" data-html="true" id="tooltip'.$part->participant_id.'" style="width: 20px; height: 20px; padding: 0; margin-right: 5px; margin-top: 2px; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px" title="Ограничений нет">&#10004;</div>';

            $orderStatus = count($orders->where(['group_participant.participant_id' => $part->participant_id])->andWhere(['group_participant.training_group_id' => $this->id])->all());
            $now_time = date("Y-m-d");
            if (($now_time < $this->finish_date && $orderStatus < 1) || ($now_time >= $this->finish_date && $orderStatus < 2))
                $result .= Html::a($part->participantWork->fullName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participant_id]), ['style' => 'color:red']);
            else
                $result .= Html::a($part->participantWork->fullName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participant_id]));
            if ($this->CheckParticipantStatus($part) == 1)
                $result .= ' <font color=red><i>ОТЧИСЛЕН</i></font>';
            else if ($this->CheckParticipantStatus($part) == 2)
                $result .= ' <font color=red><i>ПЕРЕВЕДЕН</i></font>';
            if ($part->certificat_number != '')
                $result .= ' Сертификат № ' . $part->certificat_number;
            else if ($part->certificatWork->certificat_number != '')
                $result .= ' Сертификат № ' . Html::a($part->certificatWork->CertificatLongNumber, \yii\helpers\Url::to(['certificat/view', 'id' => $part->certificatWork->id]));
            
            $cert = CertificatWork::find()->where(['training_group_participant_id' => $part->id])->one();
            if ($cert->status != 0)
                $result .= $cert->status == 1 ? ' <i><span>(отправлен)</span></i>' : ' <i><span style="color: red">(ошибка отправки)</span></i>';

            $result .= '<br>';
        }
        return $result;
    }

    public function getCountParticipants()
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $result = count($parts) . ' (включая отчисленных и переведенных)';
        return $result;
    }

    public function getPureCountParticipants()
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $result = count($parts);
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
        $result .= "<br><b><i>Всего занятий: </i>".count($parts)."</b>";
        return $result;

    }

    public function getOrdersName()
    {
        /*$orders = OrderGroupWork::find()->where(['training_group_id' => $this->id])->all();
        $result = '';
        foreach ($orders as $order)
        {
            $result .= Html::a($order->documentOrderWork->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->documentOrderWork->id])).'<br>';
        }
        return $result;*/
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $partSet = [];
        foreach ($parts as $part) $partSet[] = $part->id;
        $pasta = OrderGroupParticipantWork::find()->where(['IN','group_participant_id', $partSet])->all();
        $pastaSet = [];
        foreach ($pasta as $macaroni) $pastaSet[] = $macaroni->order_group_id;
        if (count($pastaSet) == 0)
        {
            $ogs = OrderGroupWork::find()->where(['training_group_id' => $this->id])->all();
            $ogsId = [];
            foreach ($ogs as $og) $ogsId[] = $og->document_order_id;
            $orders = DocumentOrderWork::find()->where(['IN', 'id', $ogsId])->orderBy(['order_date' => SORT_ASC])->all();
        }
        else
            $orders = DocumentOrderWork::find()->joinWith(['orderGroups orderGroups'])->where(['IN', 'orderGroups.id', $pastaSet])->orderBy(['order_date' => SORT_ASC])->all();
        foreach ($orders as $order)
        {
            $result .= Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id])).'<br>';
        }
        return $result;
    }

    public function getBranchWork()
    {
        $branch =  BranchWork::find()->where(['id' => $this->branch_id])->one();
        $result = Html::a($branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $branch->id]));
        return $result;
    }

    public function getPureBranch()
    {
        $branch =  BranchWork::find()->where(['id' => $this->branch_id])->one();
        return $branch->name;
    }

    public function getErrorsWork()
    {
        $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $errorName = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            if ($errors->getCritical() == 1)
                $result .= 'Внимание, КРИТИЧЕСКАЯ ошибка: ' . $errorName->number . ' ' . $errorName->name . '<br>';
            else $result .= 'Внимание, ошибка: ' . $errorName->number . ' ' . $errorName->name . '<br>';
        }
        return $result;
    }

    public function getColorErrors()
    {
        $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = 'default';
        foreach ($errorsList as $errors)
        {
            if ($errors->critical === 1) {
                $result = 'danger';
                break;
            } else
                $result = 'warning';
        }
        return $result;
    }

    public function getManHoursPercent()
    {
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $lessonsId = [];
        foreach ($lessons as $lesson)
            $lessonsId[] = $lesson->id;
        $visits = count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['status' => 0])->all()) + count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['status' => 2])->all());
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

    public function resetFilename($path, $file, $ext)
    {
        if (file_exists(Yii::$app->basePath . $path . $file . '.' . $ext))
        {
            $counter = 1;
            $tempName = Yii::$app->basePath . $path . $file . '.' . $ext;
            while (file_exists($tempName)) {
                if (substr($file, '-1') !== ')')
                    $file .= '(' . $counter . ')';
                else {
                    while (substr($file, -1) !== '(')
                        $file = substr($file, 0, -1);
                    $file = substr($file, 0, -1);
                    $file .= '(' . $counter . ')';
                    $counter++;
                }
                $tempName = Yii::$app->basePath . $path . $file . '.' . $ext;
            }
        }
        return $file;
    }

    public function uploadPhotosFile($upd = null)
    {
        $path = '/upload/files/training-group/photos/';
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
            $filename = 'Фото'.$counter.'_'.$new_date.'_'.$this->id;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $res = $this->resetFilename($path, $res, $file->extension);
            $path = '@app/upload/files/training-group/photos/';
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->photos = $result;
        else
            $this->photos = $this->photos.$result;
        if (strlen($result) > 3)
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлены файлы фотоматериалов '.$result);
        return true;
    }

    public function uploadPresentDataFile($upd = null)
    {
        $path = '/upload/files/training-group/present-data/';
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
            $filename = 'През'.$counter.'_'.$new_date.'_'.$this->id;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $res = $this->resetFilename($path, $res, $file->extension);
            $path = '@app/upload/files/training-group/present_data/';
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->present_data = $result;
        else
            $this->present_data = $this->present_data.$result;
        if (strlen($result) > 3)
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлены файлы презентационных материалов '.$result);
        return true;
    }

    public function uploadWorkDataFile($upd = null)
    {
        $path = '/upload/files/training-group/work-data/';
        $result = '';
        $counter = 0;
        if (strlen($this->work_data) > 3)
            $counter = count(explode(" ", $this->work_data)) - 1;
        foreach ($this->workDataFile as $file) {
            if ($file->size > _MAX_FILE_SIZE)
            {
                var_dump('Внимание: загружаемый файл (-ы) слишком большой! Максимальный размер загружаемых файлов 25Мб');
            }

            $counter++;
            $date = $this->start_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            $filename = 'Раб'.$counter.'_'.$new_date.'_'.$this->id;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $res = $this->resetFilename($path, $res, $file->extension);
            $path = '@app/upload/files/training-group/work_data/';
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->work_data = $result;
        else
            $this->work_data = $this->work_data.$result;
        if (strlen($result) > 3)
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлены файлы рабочих материалов '.$result);
        return true;
    }

    public function uploadFileParticipants()
    {
        $this->fileParticipants->saveAs('@app/upload/files/bitrix/groups/' . $this->fileParticipants->name);
        $parts = ExcelWizard::GetAllParticipants($this->fileParticipants->name);
        $this->addParticipants($parts);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлены ученики из файла '.$this->fileParticipants->name);
    }

    public function uploadFileCert()
    {
        $this->certFile->saveAs('@app/upload/files/bitrix/groups/' . $this->certFile->name);
        ExcelWizard::WriteAllCertNumbers($this->certFile->name, $this->id);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлены сертификаты из файла '.$this->certFile->name);
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
        $try = $this->hasOne(TrainingProgramWork::className(), ['id' => 'training_program_id']);
        return $try->all() ? $try : new TrainingProgramNull();
    }

    public function getCreatorString()
    {
        if ($this->creator_id !== null)
        {
            $user = UserWork::find()->where(['id' => $this->creator_id])->one();
            return $user->fullName;
        }
        else
            return '';
        
    }

    public function getEditorString()
    {
        if ($this->last_edit_id !== null)
        {
            $user = UserWork::find()->where(['id' => $this->last_edit_id])->one();
            return $user->fullName;
        }
        else
            return '';

    }

    public function GenerateNumber()
    {
        $teacher = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->orderBy(['id' => SORT_ASC])->one()->teacher_id;
        $level = $this->trainingProgramWork->level;
        $level++;
        $this->number = $this->trainingProgramWork->thematicDirection->name.'.'.$level.'.'.PeopleWork::find()->where(['id' => $teacher])->one()->short.'.'.str_replace('-', '', $this->start_date);
        $counter = count(TrainingGroupWork::find()->where(['like', 'number', $this->number.'%', false])->andWhere(['!=', 'id', $this->id])->all());
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

        return $this->number;
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
                    $visits2 = VisitWork::find()->where(['training_group_lesson_id' => $lesson->id])->andWhere(['!=', 'status', 3])->all();

                    //foreach ($themes as $theme) $theme->delete();
                    if (count($visits2) > 0 || count($themes) > 0)
                    {
                        Yii::$app->session->setFlash('danger', 'Невозможно удалить занятие, т.к. присутствуют связанные с ним сведения о явке/неявке обучающихся и/или сведения о теме занятия в учебно-тематическом плане');
                    }
                    else
                    {
                        foreach ($visits as $visit) $visit->delete();
                        if ($lesson !== null)
                            $lesson->delete();
                    }
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
            if ($this->participants !== null && $this->participants[0]->participant_name !== "") {
                usort($this->participants, array($this, "cmp"));
                $tempParts = [];
                $copyMessage = "";
                if ($this->checkOldParticipant($this->participants[0]->participant_id))
                    $tempParts[] = $this->participants[0];
                else
                    $copyMessage .= $this->participants[0]->participantWork->shortName.'<br>';
                for ($i = 1; $i < count($this->participants); $i++)
                {
                    if ($this->participants[$i - 1]->participant_id !== null && $this->participants[$i]->participant_id !== $this->participants[$i - 1]->participant_id && $this->checkOldParticipant($this->participants[$i]->participant_id))
                        $tempParts[] = $this->participants[$i];
                    else
                        $copyMessage .= $this->participants[$i]->participantWork->shortName.'<br>';
                }
                $this->participants = $tempParts;

                $currentParts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
                $currentCountParts = count($currentParts);
                $overflowFlag = 0;

                foreach ($this->participants as $participant) {
                    if ($participant->participant_id !== "")
                    {
                        if ($currentCountParts < 100)
                        {
                            $trainingParticipant = new TrainingGroupParticipant();
                            $trainingParticipant->participant_id = $participant->participant_id;
                            $trainingParticipant->certificat_number = $participant->certificat_number;
                            $trainingParticipant->send_method_id = $participant->send_method_id;
                            $trainingParticipant->training_group_id = $this->id;
                            $trainingParticipant->save();
                            Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлен обучающийся (TrainingGroupParticipant: id '.$trainingParticipant->id.')');
                            $partsArr[] = $trainingParticipant->participant_id;

                            $currentCountParts++;
                        }
                        else
                            $overflowFlag = 1;
                    }
                    else
                    {
                        $errArr[] = $participant->participant_name;
                    }
                }

                if ($overflowFlag == 1) Yii::$app->session->setFlash("danger", "Ошибка! Невозможно добавить больше 100 учеников в одну группу!");


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
                    //$newLesson->control_type_id = $lesson->control_type_id;
                    $min = $this->trainingProgram->hour_capacity;
                    $newLesson->lesson_end_time = date("H:i", strtotime('+' . $min . ' minutes', strtotime($lesson->lesson_start_time)));
                    $newLesson->duration = $this->trainingProgram->hour_capacity;
                    $aud = Auditorium::find()->where(['id' => $lesson->auds])->one();
                    $newLesson->branch_id = $lesson->auditorium_id;
                    $newLesson->auditorium_id = $aud->id;
                    $newLesson->training_group_id = $this->id;
                    if ($newLesson->checkCopyLesson())
                    {
                        $newLesson->save(false);
                        Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлено занятие (вручную, TrainingGroupLesson: id '.$newLesson->id.')');
                    }
                }
            }
            if ($this->auto[0]->day !== null && $this->auto[0]->day !== '') {
                foreach ($this->auto as $autoOne) {
                    $days = $autoOne->getDaysInRange($this->start_date, $this->finish_date);
                    foreach ($days as $day) {
                        $newLesson = new TrainingGroupLessonWork();
                        $newLesson->lesson_date = $day;
                        $newLesson->lesson_start_time = $autoOne->start_time;
                        //$newLesson->control_type_id = $autoOne->control_type_id;
                        $min = $this->trainingProgram->hour_capacity;
                        $newLesson->lesson_end_time = date("H:i", strtotime('+' . $min . ' minutes', strtotime($autoOne->start_time)));
                        $newLesson->duration = $this->trainingProgram->hour_capacity;
                        $aud = Auditorium::find()->where(['id' => $autoOne->auds])->one();
                        $newLesson->branch_id = $autoOne->auditorium_id;
                        $newLesson->auditorium_id = $aud->id;
                        $newLesson->training_group_id = $this->id;
                        if ($newLesson->checkCopyLesson())
                        {
                            $newLesson->save(false);
                            Logger::WriteLog(Yii::$app->user->identity->getId(), 'В группу '.$this->GenerateNumber().' добавлено занятие (авто, TrainingGroupLesson: id '.$newLesson->id.')');
                        }
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
                    Logger::WriteLog(Yii::$app->user->identity->getId(), 'К группе '.$this->GenerateNumber().' прикреплен приказ (OrderGroup: id '.$newOrder->id.')');
                }
            }
            if ($this->teachers !== null && $this->teachers[0]->teacher_id !== "") {
                foreach ($this->teachers as $teacher) {
                    $teacherGroup = TeacherGroup::find()->where(['teacher_id' => $teacher->teacher_id])->andWhere(['training_group_id' => $this->id])->one();
                    if ($teacherGroup === null)
                        $teacherGroup = new TeacherGroup();
                    $teacherGroup->teacher_id = $teacher->teacher_id;
                    $teacherGroup->training_group_id = $this->id;
                    $teacherGroup->save();
                    Logger::WriteLog(Yii::$app->user->identity->getId(), 'К группе '.$this->GenerateNumber().' прикреплен педагог (TeacherGroup: id '.$teacherGroup->id.')');
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
                        $theme->control_type_id = $tp[$i]->control_type_id;
                        $theme->training_group_lesson_id = $lessons[$i]->id;
                        $theme->teacher_id = $teachers[0]->teacher_id;
                        $theme->save(false);
                        Logger::WriteLog(Yii::$app->user->identity->getId(), 'К группе '.$this->GenerateNumber().' прикреплена тема занятия (LessonTheme: id '.$theme->id.')');
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

            //блок сохранения тем проектов
            if ($this->themes !== null)
            {
                $str = '';
                for ($i = 0; $i < count($this->themes); $i++)
                {
                    $tempId = -1;

                    $pt = new ProjectThemeWork();
                    $pt->name = $this->themes[$i]->themeName;
                    $pt->description = $this->themes[$i]->themeDescription;
                    if ($pt->save())
                        $tempId = $pt->id;
                        
                    $gpt = new GroupProjectThemesWork();
                    $gpt->training_group_id = $this->id;
                    $gpt->project_theme_id = $tempId;
                    $gpt->project_type_id = $this->themes[$i]->project_type_id;
                    if ($this->themes[$i]->project_type_id == "" && $this->themes[$i]->themeName !== "")
                        $str .= 'Не указан тип проекта для темы '.$this->themes[$i]->themeName.'<br>';
                    else
                        $gpt->save();
                }
                if ($str != "")
                    Yii::$app->session->setFlash('danger', $str);
            }

            //блок сохранения приглашенных экспертов
            if ($this->experts !== null && $this->experts[0]->expert_id !== '')
            {
                $exs = TrainingGroupExpertWork::find()->where(['training_group_id' => $this->id])->all();
                if (count($exs) + count($this->experts) > 5)
                {
                    Yii::$app->session->setFlash('danger', 'Попытка добавить более 5 экспертов! Некоторые эксперты не будут добавлены');
                    
                }

                for ($i = 0; $i < count($this->experts) && $i + count($exs) < 5; $i++)
                {
                    $ex = new TrainingGroupExpertWork();
                    if (TrainingGroupExpertWork::find()->where(['expert_id' => $this->experts[$i]->expert_id])->andWhere(['training_group_id' => $this->id])->one() !== null)
                        Yii::$app->session->setFlash('danger', 'Попытка добавить дубликат эксперта!');
                    else
                    {
                        $ex->expert_id = $this->experts[$i]->expert_id;
                        $ex->expert_type_id = $this->experts[$i]->expert_type_id + 1;
                        $ex->training_group_id = $this->id;

                        $ex->save();
                    }
                    
                }
                    
            }

            // тут должны работать проверки на ошибки
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckErrorsTrainingGroupWithoutAmnesty($this->id);
        }
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id === null)
            $this->creator_id = Yii::$app->user->identity->getId();
        $this->last_edit_id = Yii::$app->user->identity->getId();
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        $orders = OrderGroup::find()->where(['training_group_id' => $this->id])->all();

        if (count($orders) > 0)
        {
            Yii::$app->session->setFlash('danger', 'Невозможно удалить группу, т.к. имеются связанные с ней приказы!');
            return;
        }

        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $teachers = TeacherGroupWork::find()->where(['training_group_id' => $this->id])->all();
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

    public function InfoProtectionGroup($user_id)
    {
        $dateCheck = date('Y-m-d', strtotime(date("Y-m-d") . '-14 day'));
        $groups = TrainingGroupWork::find()->joinWith(['groupProjectThemes theme'])
            ->where(['archive' => 0]);

        $flag = false;
        $result = '';

        if (RoleBaseAccess::CheckRole($user_id, 2) || RoleBaseAccess::CheckRole($user_id, 3) ||
                RoleBaseAccess::CheckRole($user_id, 4) || RoleBaseAccess::CheckRole($user_id, 7) || RoleBaseAccess::CheckRole($user_id, 8))
            $flag = true;

        if (RoleBaseAccess::CheckRole($user_id, 6))
        {
            $groupsTheme = $groups->all();

            if (empty($groupsTheme))
                $flag = true;
            else
            {

                $result .= '<h4 style="font-size: 16px; font-weight: 600;">Необходимо подтверждение (удаление) тем проектов для следующих учебных групп:</h4>';
                $result .= '<table style="width: 700px; font-size: 15px; margin-bottom: 50px;" class="table table-bordered"><thead><tr><td>Номер учебной группы</td><td>Дата защиты группы</td><td>Дата окончания занятий</td></tr></thead>';
                foreach ($groupsTheme as $group)
                {
                    if (!empty($group->groupProjectThemes))
                    {
                        foreach ($group->groupProjectThemes as $theme)
                            if ($theme->confirm == 0)
                            {
                                $result .= '<tr><td>'. $group->getNumberView() . '</td><td>'.$group->protection_date.'</td><td>'.$group->finish_date.'</td></tr>';
                                break;
                            }
                    }
                }
                $result .= '</table>';
            }
        }
        if (RoleBaseAccess::CheckRole($user_id, 5))
        {
            $user = UserWork::find()->where(['id' => $user_id])->one();
            $branchID = PeopleWork::find()->where(['id' => $user->aka])->one()->branch_id;
            $groupsConfirm = $groups->andWhere(['>=','finish_date', $dateCheck])->andWhere(['!=', 'protection_confirm', 1])->andWhere(['branch_id' => $branchID])->all();

            if (empty($groupsConfirm))
                $flag = true;
            else
            {
                $result .= '<h4 style="font-size: 16px; font-weight: 600;">Необходимо подтвердить допуск следующих учебных групп к защите:</h4>';
                $result .= '<table style="width: 700px; font-size: 15px; margin-bottom: 50px;" class="table table-bordered"><thead><tr><td>Номер учебной группы</td><td>Дата защиты группы</td><td>Дата окончания занятий</td></tr></thead>';
                foreach ($groupsConfirm as $group)
                {
                    if (!empty($group->groupProjectThemes))
                    {
                        foreach ($group->groupProjectThemes as $theme)
                            if ($theme->confirm == 1)
                            {
                                $result .= '<tr><td>'. $group->getNumberView() . '</td><td>'.$group->protection_date.'</td><td>'.$group->finish_date.'</td></tr>';
                                break;
                            }
                    }
                }
                $result .= '</table>';
            }
        }
        if (RoleBaseAccess::CheckRole($user_id, 1))
        {
            $user = UserWork::find()->where(['id' => $user_id])->one();
            $groupsTeacher = $groups->andWhere(['>=','finish_date', $dateCheck])->joinWith(['teacherGroups teacherGroups'])->where(['teacherGroups.teacher_id' => $user->aka])->all();

            if(empty($groupsTeacher))
                $flag = true;
            else
            {
                $result .= '<h4 style="font-size: 16px; font-weight: 600;">Необходимо заполнить сведения о защите для следующих учебных групп:</h4>';
                $result .= '<table style="width: 700px; font-size: 15px; margin-bottom: 50px;" class="table table-bordered"><thead><tr><td>Номер учебной группы</td><td>Дата окончания занятий</td></tr></thead>';
                foreach ($groupsTeacher as $group)
                {
                    $experts = TrainingGroupExpertWork::find()->where(['training_group_id' => $group->id])->all();
                    $themes = GroupProjectThemesWork::find()->where(['training_group_id' => $group->id])->all();

                    if ($group->protection_date == null || empty($experts) || empty($themes))
                        $result .= '<tr><td>'. $group->getNumberView() . '</td><td>'.$group->finish_date.'</td></tr>';

                }
                $result .= '</table>';


                $result .= '<h4 style="font-size: 16px; font-weight: 600; max-width: 900px;">Необходимо заполнить одобренную тему проекта или оценку каждому обучающемуся в электронном журнале для следующих учебных групп:</h4>';
                $result .= '<table style="width: 700px; font-size: 15px; margin-bottom: 50px;" class="table table-bordered"><thead><tr><td>Номер учебной группы</td><td>Дата защиты группы</td><td>Дата окончания занятий</td></tr></thead>';
                foreach ($groupsTeacher as $group)
                {
                    $themes = GroupProjectThemesWork::find()->where(['training_group_id' => $group->id])->andWhere(['confirm' => 1])->all();
                    $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->orWhere(['IS', 'points', null])->orWhere(['IS', 'success', null])->orWhere(['IS', 'group_project_themes_id', null])->all();

                    if ($group->trainingProgram->certificat_type_id != 3)
                    if (empty($themes) || empty($parts))
                        $result .= '<tr><td>'. $group->getNumberView() . '</td><td>'.$group->protection_date.'</td><td>'.$group->finish_date.'</td></tr>';

                }
                $result .= '</table>';

                $result .= '<h4 style="font-size: 16px; font-weight: 600;">Необходимо проставить отметку успешного завершения обучения обучающимся (в электронном журнале)</h4>';
                $result .= '<table style="width: 700px; font-size: 15px; margin-bottom: 50px;" class="table table-bordered"><thead><tr><td>Номер учебной группы</td><td>Дата защиты группы</td><td>Дата окончания занятий</td></tr></thead>';
                foreach ($groupsTeacher as $group)
                {
                    if ($group->protection_confirm == 1)
                        $result .= '<tr><td>'. $group->getNumberView() . '</td><td>'.$group->protection_date.'</td><td>'.$group->finish_date.'</td></tr>';

                }
                $result .= '</table>';
            }
        }

        if ($flag && $result == '')
            echo 'Данный раздел работает. Но для Вас информации не нашлось.';

        return $result;
            //->andWhere(['theme.confirm' => 0])->all();
        /*$groupsID = [];
        foreach ($groups as $group)
            $groupsID[] = $group->id;*/

        //$grPrTheme =

    }
}
