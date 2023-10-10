<?php


namespace app\models\extended;


use app\models\common\ParticipantAchievement;
use app\models\common\TrainingGroup;
use app\models\components\report\DebugReportFunctions;
use app\models\components\report\ReportConst;
use app\models\components\report\SupportReportFunctions;
use app\models\work\ForeignEventWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\LessonThemeWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\PeopleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\VisitWork;
use DateTime;
use Mpdf\Tag\P;
use yii\db\Query;

class ForeignEventReportModel extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $branch;
    public $focus;
    public $budget;
    public $prize;
    public $level;
    public $allow_remote;


    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['focus', 'branch', 'budget', 'prize', 'level', 'allow_remote'], 'safe'],

        ];
    }

    static public function GetPrizesWinners($event_level, $events_id, $events_id2, $start_date, $end_date, $branch_id, $focus_id, $allow_remote_id, $participants_not_include)
    {
        $debug = '';

        $not_include = $participants_not_include;

        if ($events_id == 0)
            $events1 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $branch_id])->all();
        else
            $events1 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'id', $events_id])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $branch_id])->all();

        $partsLink = null;
        $pIds = [];
        $eIds = [];
        foreach ($events1 as $event) $eIds[] = $event->id;
        if ($branch_id !== 0)
        {
            
            if ($focus_id !== 0)
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['IN', 'teacherParticipant.focus', $focus_id])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();
            else
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();


        }
        else
        {
            $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();
        }

        foreach ($partsLink as $part) $pIds[] = $part->teacherParticipant->participant_id;
        foreach ($pIds as $one) $not_include[] = $one;


        $counter1 = 0;
        $counter2 = 0;
        $counterPart1 = 0;
        $allTeams = 0;
        foreach ($events1 as $event)
        {
            $realParts = [];
            if ($focus_id !== 0)
                $realParts = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $event->id])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['IN', 'teacherParticipant.focus', $focus_id])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();
            else
                $realParts = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['IN', 'teacherParticipant.focus', $focus_id])->all();


            $realPartsId = [];
            foreach ($realParts as $one) $realPartsId[] = $one->teacherParticipant->participant_id;

            //ОТЛАДКА
            $debug .= $event->name.";".$event->company->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
            //ОТЛАДКА
            $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();


            $tIds = [];
            $teamName = '';
            $counterTeamWinners = 0;
            $counterTeamPrizes = 0;
            $counterTeam = 0;
            foreach ($teams as $team)
            {

                if ($teamName != $team->name)
                {
                    $res = null;
                    $teamName = $team->name;
                    if ($partsLink !== null)
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $realPartsId])->one();
                    else
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();

                    if ($res !== null) $counterTeamWinners++;
                    else
                    {
                        if ($partsLink !== null)
                            $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $realPartsId])->one();
                        else
                            $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 0])->one();

                        if ($res !== null) $counterTeamPrizes++;
                    }

                    
                    if ($partsLink !== null)
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'participant_id', $realPartsId])->one();
                    else
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                
                    
                }
                $tIds[] = $team;
            }


            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            if ($partsLink !== null)
            {

                if ($events_id2 == 0)
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $realPartsId])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $realPartsId])->all();
                }
                else
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'foreign_event_id', $events_id2])->andWhere(['IN', 'participant_id', $realPartsId])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'foreign_event_id', $events_id2])->andWhere(['IN', 'participant_id', $realPartsId])->all();
                }
                
            }
            else
            {
                if ($events_id2 == 0)
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->all();
                }
                else
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'foreign_event_id', $events_id2])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'foreign_Event_id', $events_id2])->all();
                }
                
            }
            


            $counter1 += count($achieves1) + $counterTeamPrizes;
            $counter2 += count($achieves2) + $counterTeamWinners;
            $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $branch_id])->andWhere(['IN', 'allow_remote_id', $allow_remote_id])->all()) + $counterTeam;
            $allTeams += $counterTeam;


            //ОТЛАДКА
            $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->all();
            $s1 = count($achieves1);
            $s2 = count($achieves2);
            $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
            $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
            $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';

            //if ($event->id == 352)
            //    var_dump(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $branch_id])->andWhere(['IN', 'allow_remote_id', $allow_remote_id])->createCommand()->getRawSql());


            $debug .= count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $branch_id])->andWhere(['IN', 'allow_remote_id', $allow_remote_id])->andWhere(['IN', 'participant_id', $realPartsId])->all()).";".$counterTeam.";".$s1.";".$counterTeamPrizes.";".$s2.";".$counterTeamWinners."\r\n";
            //ОТЛАДКА

        }

        return [$counter1, $counter2, $not_include, $counterPart1, $debug];
    }

    static public function GetParticipantsIdsFromGroups($group_ids)
    {
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $group_ids])->all(); //получаем всех учеников из групп

        $result = [];
        foreach ($participants as $participant) $result[] = $participant->participant_id;

        return $result;

    }


    //--Новый генератор отчетов--

    public function generateReportNew()
    {
        $resultHTML = "<table class='table table-bordered'><tr><td><b>Наименование показателя</b></td><td><b>Значение показателя</b></td></tr>";

        $participantsMoreThatCity = 0;
        $achievesMoreThatCity = 0;

        $events = [];

        foreach ($this->level as $level)
        {
            $result = SupportReportFunctions::GetParticipants(ReportConst::PROD,
                $this->start_date, $this->end_date,
                TeamWork::TEAM_ON, 0,
                $level, $this->branch, $this->focus, $this->allow_remote);

            $events = array_merge($events, $result[5]);

            $prizes = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $result, 0, [ParticipantAchievementWork::PRIZE])[0];
            $winners = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $result, 0, [ParticipantAchievementWork::WINNER])[0];

            if ($level >= 6)
            {
                $participantsMoreThatCity += count($result[0]);
                $achievesMoreThatCity += count($prizes) + count($winners);
            }

            $levelStr = "международных";
            if ($level == 3) $levelStr = "внутренних";
            if ($level == 4) $levelStr = "районных";
            if ($level == 5) $levelStr = "городских";
            if ($level == 6) $levelStr = "региональных";
            if ($level == 7) $levelStr = "федеральных";
            if ($level == 9) $levelStr = "межрегиональных";

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками $levelStr конкурсных мероприятий</td><td>".$result[2]."</td></tr>";
            $resultHTML .= "<tr><td>Число учащихся, являющихся призерами $levelStr конкурсных мероприятий</td><td>".count($prizes)."</td></tr>";
            $resultHTML .= "<tr><td>Число учащихся, являющихся победителями $levelStr конкурсных мероприятий</td><td>".count($winners)."</td></tr>";
        }

        $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами мероприятий, не ниже регионального уровня</td><td>".round($achievesMoreThatCity * 1.0 / $participantsMoreThatCity, 2)."</td></tr>";
        $resultHTML .= "</table>";

        $events = ForeignEventWork::find()->where(['IN', 'id', $events])->all();

        $debugData = DebugReportFunctions::DebugDataForeignEvents($events, $this->branch, $this->focus, $this->allow_remote);
        $header = "Отчет по учету достижений в мероприятиях за период с ".$this->start_date." по ".$this->end_date;

        return [$resultHTML, $debugData, $header];
    }

    //---------------------------


    public function generateReport()
    {
        $header = "Отчет по учету достижений в мероприятиях за период с ".$this->start_date." по ".$this->end_date;
        //ОТЛАДКА
        $debug = "Мероприятие;Организатор;Уровень;Дата начала;Дата окончания;Кол-во инд. участников;Кол-во команд;Призеры инд.;Призеры-команды;Победители инд.;Победители-команды\r\n";
        //ОТЛАДКА

        //Получаем группы и учеников

        $tgIds = [];


        $trainingGroups1 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $this->start_date])->andWhere(['>', 'finish_date', $this->end_date])->andWhere(['<', 'start_date', $this->end_date])->andWhere(['IN', 'budget', $this->budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $this->start_date])->andWhere(['<', 'finish_date', $this->end_date])->andWhere(['>', 'finish_date', $this->start_date])->andWhere(['IN', 'budget', $this->budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $this->start_date])->andWhere(['>', 'finish_date', $this->end_date])->andWhere(['IN', 'budget', $this->budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $this->start_date])->andWhere(['<', 'finish_date', $this->end_date])->andWhere(['IN', 'budget', $this->budget])])
            ->all();

        foreach ($trainingGroups1 as $trainingGroup) $tgIds[] = $trainingGroup->id;

        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->andWhere(['IN', 'participant_id', ForeignEventReportModel::GetParticipantsIdsFromGroups($tgIds)])->all();

        //--------------------------

        //Получаем мероприятия с выбранными учениками

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;
        $eventParticipants = TeacherParticipantWork::find()->where(['IN', 'participant_id', $pIds])->all();

        $eIds = [];
        foreach ($eventParticipants as $eventParticipant) $eIds[] = $eventParticipant->foreign_event_id;

        $eIds2 = [];
        foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

        $events = ForeignEventWork::find()->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date]);

        //-------------------------------------------

        //======РЕЗУЛЬТАТ======
        $resultHTML = "<table class='table table-bordered'><tr><td><b>Наименование показателя</b></td><td><b>Значение показателя</b></td></tr>";
        //Вывод ВСЕХ обучающихся (по группам)
        //$resultHTML .= "<tr><td>Общее число обучающихся</td><td>".count($participants)."</td></tr>";
        //-----------------------------------
        $counterTeam = 0;

        $bigCounter = 0;
        $bigPrizes = 0;
        //Вывод количества призеров / победителей (международных)
        if (array_search(8, $this->level) !== false)
        {
            $result = ForeignEventReportModel::GetPrizesWinners(8, 0, 0, $this->start_date, $this->end_date, $this->branch, $this->focus, $this->allow_remote, []);
            $debug .= $result[4];

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($result[3] !== 0)
            {
                $r1 = ($result[0] * 1.0) / ($result[3] * 1.0);
                $r2 = ($result[1] * 1.0) / ($result[3] * 1.0);
                $r3 = (($result[0] + $result[1]) * 1.0) / ($result[3] * 1.0);
                $bigCounter += $result[3];
                $bigPrizes += $result[0] + $result[1];
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками международных конкурсных мероприятий</td><td>".$result[3].$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".$result[0]."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".$result[1]."</td></tr>";

        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (федеральных)
        if (array_search(7, $this->level) !== false)
        {
            $result = ForeignEventReportModel::GetPrizesWinners(7, 0, 0, $this->start_date, $this->end_date, $this->branch, $this->focus, $this->allow_remote, []);
            $debug .= $result[4];

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($result[3] !== 0)
            {
                $r1 = ($result[0] * 1.0) / ($result[3] * 1.0);
                $r2 = ($result[1] * 1.0) / ($result[3] * 1.0);
                $r3 = (($result[0] + $result[1]) * 1.0) / ($result[3] * 1.0);
                $bigCounter += $result[3];
                $bigPrizes += $result[0] + $result[1];
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками федеральных конкурсных мероприятий</td><td>".$result[3].$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами федеральных конкурсных мероприятий</td><td>".$result[0]."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями федеральных конкурсных мероприятий</td><td>".$result[1]."</td></tr>";

        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (региональных)
        if (array_search(6, $this->level) !== false)
        {
            $result = ForeignEventReportModel::GetPrizesWinners(6, 0, 0, $this->start_date, $this->end_date, $this->branch, $this->focus, $this->allow_remote, []);
            $debug .= $result[4];

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($result[3] !== 0)
            {
                $r1 = ($result[0] * 1.0) / ($result[3] * 1.0);
                $r2 = ($result[1] * 1.0) / ($result[3] * 1.0);
                $r3 = (($result[0] + $result[1]) * 1.0) / ($result[3] * 1.0);
                $bigCounter += $result[3];
                $bigPrizes += $result[0] + $result[1];
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками региональных конкурсных мероприятий</td><td>".$result[3].$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".$result[0]."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".$result[1]."</td></tr>";

            if ($bigCounter == 0)
                $bigPercent = 0;
            else
            $bigPercent = ($bigPrizes * 1.0) / ($bigCounter * 1.0);
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами мероприятий, не ниже регионального уровня</td><td>".round($bigPercent, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (городских)
        if (array_search(5, $this->level) !== false)
        {
            $result = ForeignEventReportModel::GetPrizesWinners(5, 0, 0, $this->start_date, $this->end_date, $this->branch, $this->focus, $this->allow_remote, []);
            $debug .= $result[4];

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($result[3] !== 0)
            {
                $r1 = ($result[0] * 1.0) / ($result[3] * 1.0);
                $r2 = ($result[1] * 1.0) / ($result[3] * 1.0);
                $r3 = (($result[0] + $result[1]) * 1.0) / ($result[3] * 1.0);
                $bigCounter += $result[3];
                $bigPrizes += $result[0] + $result[1];
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками городских конкурсных мероприятий</td><td>".$result[3].$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами городских конкурсных мероприятий</td><td>".$result[0]."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями городских конкурсных мероприятий</td><td>".$result[1]."</td></tr>";

        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (районных)
        if (array_search(4, $this->level) !== false)
        {

            $result = ForeignEventReportModel::GetPrizesWinners(4, 0, 0, $this->start_date, $this->end_date, $this->branch, $this->focus, $this->allow_remote, []);
            $debug .= $result[4];

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($result[3] !== 0)
            {
                $r1 = ($result[0] * 1.0) / ($result[3] * 1.0);
                $r2 = ($result[1] * 1.0) / ($result[3] * 1.0);
                $r3 = (($result[0] + $result[1]) * 1.0) / ($result[3] * 1.0);
                $bigCounter += $result[3];
                $bigPrizes += $result[0] + $result[1];
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками районных конкурсных мероприятий</td><td>".$result[3].$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами районных конкурсных мероприятий</td><td>".$result[0]."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями районных конкурсных мероприятий</td><td>".$result[1]."</td></tr>";

        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (внутренние)
        if (array_search(3, $this->level) !== false)
        {
            $result = ForeignEventReportModel::GetPrizesWinners(3, 0, 0, $this->start_date, $this->end_date, $this->branch, $this->focus, $this->allow_remote, []);
            $debug .= $result[4];

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($result[3] !== 0)
            {
                $r1 = ($result[0] * 1.0) / ($result[3] * 1.0);
                $r2 = ($result[1] * 1.0) / ($result[3] * 1.0);
                $r3 = (($result[0] + $result[1]) * 1.0) / ($result[3] * 1.0);
                $bigCounter += $result[3];
                $bigPrizes += $result[0] + $result[1];
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками внутренних конкурсных мероприятий</td><td>".$result[3].$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами внутренних конкурсных мероприятий</td><td>".$result[0]."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями внутренних конкурсных мероприятий</td><td>".$result[1]."</td></tr>";


        }
        //-----------------------------------------
        //=====================
        $resultHTML .= "</table>";
        return [$resultHTML, $debug, $header];
    }

    public function getAge($birthdate, $target_date)
    {
        $bdTime = new DateTime($birthdate);
        $tdTime = new DateTime($target_date);
        $interval = $tdTime->diff($bdTime);
        return $interval->y;
    }

    public function save()
    {
        return true;
    }
}