<?php


namespace app\models\extended;


use app\models\common\TrainingGroup;
use app\models\components\report\DebugReportFunctions;
use app\models\components\report\ReportConst;
use app\models\components\report\SupportReportFunctions;
use app\models\work\LessonThemeWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeamWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\VisitWork;
use app\models\work\BranchProgramWork;
use app\models\components\ExcelWizard;
use app\models\work\TrainingGroupExpertWork;
use app\models\work\PeoplePositionBranchWork;
use Mpdf\Tag\P;
use yii\db\Query;

class ManHoursReportModel extends \yii\base\Model
{
    const MAN_HOURS_REPORT = 0;
    const PARTICIPANTS_REPORT = 1;
    const PARTICIPANTS_UNIQUE_REPORT = 2;


    public $start_date;
    public $end_date;
    public $type;
    public $unic;
    /*
     * 0 - человеко-часы
     * 1 - всего уникальных людей
     * 2 - всего людей
     */
    public $branch;
    public $budget;
    public $teacher;
    public $focus;
    public $allow_remote;
    public $method;


    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['type', 'branch', 'budget', 'focus', 'allow_remote'], 'safe'],
            [['method', 'teacher', 'unic'], 'integer']
        ];
    }


    private function generateView($data, $type)
    {
        $result = '';

        if ($type == ManHoursReportModel::MAN_HOURS_REPORT)
        {
            $result .= '<tr><td>Количество человеко-часов за период с '.$this->start_date.' по '.$this->end_date.
                '</td><td>'.count($data).' ч/ч'.'</td></tr>';
        }
        else if ($type == ManHoursReportModel::PARTICIPANTS_REPORT)
        {
            $result .= $data[0] == -1 ? '' : '<tr><td><b>1</b></td><td>Количество обучающихся, начавших обучение до '.$this->start_date.' и завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[0]. ' чел.'.'</td></tr>';
            $result .= $data[1] == -1 ? '' : '<tr><td><b>2</b></td><td>Количество обучающихся, начавших обучение в период с '.$this->start_date.' по '.$this->end_date.' и завершивших обучение после '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[1]. ' чел.'.'</td></tr>';
            $result .= $data[2] == -1 ? '' : '<tr><td><b>3</b></td><td>Количество обучающихся, начавших обучение после '.$this->start_date.' и завершивших до '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[2]. ' чел.'.'</td></tr>';
            $result .= $data[3] == -1 ? '' : '<tr><td><b>4</b></td><td>Количество обучающихся, начавших обучение до '.$this->start_date.' и завершивших после '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[3]. ' чел.'.'</td></tr>';
        }
        else if ($type == ManHoursReportModel::PARTICIPANTS_UNIQUE_REPORT)
        {
            $result .= '<tr><td>Общее количество уникальных обучающихся</td><td>'.count($data).'</td></tr>';
        }

        return $result;
    }


    public function generateReport()
    {
        //ini_set('max_execution_time', '6000');
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        //--Основные отчетные данные--
        //Ожидается массив, если -1 - значит соответствующий пункт не выбран
        $gp1 = -1;
        $gp2 = -1;
        $gp3 = -1;
        $gp4 = -1;

        $groups1Id = [];
        $groups2Id = [];
        $groups3Id = [];
        $groups4Id = [];

        $groupParticipants1 = [];
        $groupParticipants2 = [];
        $groupParticipants3 = [];
        $groupParticipants4 = [];
        //----------------------------

        $debugCSV = "Группа;Кол-во занятий выбранного педагога;Кол-во занятий всех педагогов;Кол-во учеников;Кол-во ч/ч\r\n";
        $debugCSV2 = "ФИО обучающегося;Группа;Дата начала занятий;Дата окончания занятий;Отдел;Пол;Дата рождения;Направленность;Педагог;Основа;Тематическое направление;Образовательная программа;Тема проекта;Дата защиты;Тип проекта;ФИО эксперта;Тип эксперта;Место работы эксперта;Должность эксперта;Раздел\r\n";


        $mainHeader = "<b>Отчет по</b><br>";
        $firstHeader = '';
        $secondHeader = '';


        foreach ($this->type as $oneType)
        {

            if ($oneType == '0')
            {
                if ($firstHeader == '') $firstHeader = "человеко-часам<br>";

                //--ОТЧЕТ ПО ЧЕЛОВЕКО-ЧАСАМ--

                //--Основной алгоритм--

                $groups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD,
                    $this->start_date, $this->end_date,
                    $this->branch,
                    $this->focus,
                    $this->allow_remote,
                    $this->budget,
                    $this->teacher == '' ? [] : $this->teacher);

                $participants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $groups, 0, ReportConst::AGES_ALL, date('Y-m-d'));

                $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $participants, $this->start_date, $this->end_date, $this->method == 0 ? VisitWork::ONLY_PRESENCE : VisitWork::PRESENCE_AND_ABSENCE/*, $this->teacher == null ? [] : [$this->teacher]*/);

                //---------------------


                //--Отладочная информация--

                $debugManHours = DebugReportFunctions::DebugDataManHours($groups,
                    $this->start_date, $this->end_date,
                    $this->method == 0 ? VisitWork::ONLY_PRESENCE : VisitWork::PRESENCE_AND_ABSENCE,
                    $this->teacher == '' ? [] : $this->teacher);



                foreach ($debugManHours as $one)
                    $debugCSV .= $one->group.";".
                        count($one->lessonsChangeTeacher).";".
                        count($one->lessonsAll).";".
                        count($one->participants).";".
                        count($one->manHours)."\r\n";

                //-------------------------

                $resultManHours = $this->generateView($visits, ManHoursReportModel::MAN_HOURS_REPORT);

                //---------------------------
            }
            else
            {
                if ($secondHeader == '') $secondHeader = "обучающимся<br>";

                //--ОТЧЕТ ПО КОЛИЧЕСТВУ ОБУЧАЮЩИХСЯ--

                //--Основной алгоритм--

                if ($oneType == '1')
                {
                    $groups1 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_EARLY_END_IN]);

                    $groups1Id = SupportReportFunctions::GetIdFromArray($groups1);


                    $groupParticipants1 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups1Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups1Id])->all();

                    $gp1 = count($groupParticipants1);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(1, $groupParticipants1, $this->unic, SupportReportFunctions::GetIdFromArray($groups1));

                }

                if ($oneType == '2')
                {
                    $groups2 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_IN_END_LATER]);

                    $groups2Id = SupportReportFunctions::GetIdFromArray($groups2);

                    $groupParticipants2 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups2Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups2Id])->all();

                    $gp2 = count($groupParticipants2);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(2, $groupParticipants2, $this->unic, SupportReportFunctions::GetIdFromArray($groups2));

                }

                if ($oneType == '3')
                {
                    $groups3 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_IN_END_IN]);

                    $groups3Id = SupportReportFunctions::GetIdFromArray($groups3);

                    $groupParticipants3 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups3Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups3Id])->all();

                    $gp3 = count($groupParticipants3);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(3, $groupParticipants3, $this->unic, SupportReportFunctions::GetIdFromArray($groups3));

                }

                if ($oneType == '4')
                {
                    $groups4 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_EARLY_END_LATER]);

                    $groups4Id = SupportReportFunctions::GetIdFromArray($groups4);

                    $groupParticipants4 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups4Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups4Id])->all();

                    $gp4 = count($groupParticipants4);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(4, $groupParticipants4, $this->unic, SupportReportFunctions::GetIdFromArray($groups4));

                }

                //---------------------

                if ($this->unic == 0)
                    $resultParticipantCount = $this->generateView([$gp1, $gp2, $gp3, $gp4], ManHoursReportModel::PARTICIPANTS_REPORT);

                //-----------------------------------
            }
        }


        //--Отладочная информация--

        if ($this->unic == 1)
        {
            $allGroups = array_merge($groups1Id, array_merge($groups2Id, array_merge($groups3Id, $groups4Id)));

            $allParticipants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $allGroups])->all();

            $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(0, $allParticipants, $this->unic, $allGroups);

            $resultParticipantCount = $this->generateView($allParticipants, ManHoursReportModel::PARTICIPANTS_UNIQUE_REPORT);
        }

        //-------------------------


        $result = '<table class="table table-bordered">';

        $result .= $resultManHours;
        $result .= $resultParticipantCount;

        $result .= '</table>';



        return [$mainHeader.$firstHeader.$secondHeader, $result, $debugCSV, $debugCSV2];
    }


    public function save()
    {
        return true;
    }
}