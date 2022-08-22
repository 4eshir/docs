<?php


namespace app\models\extended;


use app\models\common\ParticipantAchievement;
use app\models\common\TrainingGroup;
use app\models\work\ForeignEventWork;
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

    public function generateReport()
    {
        $header = "Отчет по учету достижений в мероприятиях за период с ".$this->start_date." по ".$this->end_date;
        //ОТЛАДКА
        $debug = "Мероприятие;Уровень;Дата начала;Дата окончания;Кол-во участников;Призеры;Победители;\r\n";
        //ОТЛАДКА

        //Получаем группы и учеников

        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
            //->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
            ->andWhere(['IN', 'budget', $this->budget])
            ->all();


        $tgIds = [];
        foreach ($trainingGroups as $trainingGroup) $tgIds[] = $trainingGroup->id;
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->all();

        //--------------------------

        //Получаем мероприятия с выбранными учениками

        $events = ForeignEventWork::find()->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date]);

        $newEv = $events->all();

        $eventIds = [];
        foreach ($newEv as $event) $eventIds[] = $event->id;

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;
        $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'foreign_event_id', $eventIds])->all();


        $eIds = [];
        foreach ($eventParticipants as $eventParticipant) $eIds[] = $eventParticipant->foreign_event_id;

        $eIds2 = [];
        foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

        var_dump(count($events->all()));

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
            $events1 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 8])->andWhere(['teacherParticipantBranches.branch_id' => $this->branch])->all();

            $e2 = [];
            foreach ($events1 as $event) $e2[] = $event->id;

            $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'foreign_event_id', $e2])->all();


            $eIds2 = [];
            foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

            $counter1 = 0;
            $counter2 = 0;
            $counterPart1 = 0;
            $allTeams = 0;
            foreach ($events1 as $event)
            {
                //ОТЛАДКА
                $debug .= $event->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
                //ОТЛАДКА
                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                $counterTeam = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team;
                }

                $tpIds = [];
                foreach ($tIds as $tId)
                    $tpIds[] = $tId->participant_id;

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();

                //var_dump($eIds2);


                $counter1 += count($achieves1) + $counterTeamPrizes;
                $counter2 += count($achieves2) + $counterTeamWinners;
                $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam;
                $allTeams += $counterTeam;

                //ОТЛАДКА
                $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= (count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam).$teamStr.";".$s1.$teamPrizeStr.";".$s2. $teamWinnersStr."\r\n";
                //ОТЛАДКА

            }

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($counterPart1 !== 0)
            {
                $r1 = ($counter1 * 1.0) / ($counterPart1 * 1.0);
                $r2 = ($counter2 * 1.0) / ($counterPart1 * 1.0);
                $r3 = (($counter1 + $counter2) * 1.0) / ($counterPart1 * 1.0);
                $bigCounter += $counterPart1;
                $bigPrizes += $counter1 + $counter2;
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками международных конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".$counter1."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".$counter2."</td></tr>";

            //if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            //if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            //if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами международных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";
        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (всероссийских)
        if (array_search(7, $this->level) !== false)
        {
            $events2 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 7])->andWhere(['teacherParticipantBranches.branch_id' => $this->branch])->all();

            $e2 = [];
            foreach ($events2 as $event) $e2[] = $event->id;

            $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'foreign_event_id', $e2])->all();


            $eIds2 = [];
            foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;


            $counter3 = 0;
            $counter4 = 0;
            $counterPart1 = 0;
            $allTeams = 0;
            foreach ($events2 as $event)
            {
                //ОТЛАДКА
                $debug .= $event->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                $counterTeam = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team;
                }

                $tpIds = [];
                foreach ($tIds as $tId)
                    $tpIds[] = $tId->participant_id;

                //$allAchievBranch = ParticipantAchievementWork::find()

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();

                //var_dump($achieves1[0]->id);

                $counter3 += count($achieves1) + $counterTeamPrizes;
                $counter4 += count($achieves2) + $counterTeamWinners;
                $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam;
                $allTeams += $counterTeam;

                //ОТЛАДКА
                $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= (count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam).$teamStr.";".$s1.$teamPrizeStr.";".$s2. $teamWinnersStr."\r\n";
                
                //ОТЛАДКА
            }

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($counterPart1 !== 0)
            {
                $r1 = ($counter3 * 1.0) / ($counterPart1 * 1.0);
                $r2 = ($counter4 * 1.0) / ($counterPart1 * 1.0);
                $r3 = (($counter3 + $counter4) * 1.0) / ($counterPart1 * 1.0);

                $bigCounter += $counterPart1;
                $bigPrizes += $counter3 + $counter4;
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками всероссийских конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами всероссийских конкурсных мероприятий</td><td>".$counter3."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями всероссийских конкурсных мероприятий</td><td>".$counter4."</td></tr>";

            //if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами всероссийских конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            //if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями всероссийских конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            //if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами всероссийских конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";

        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (региональных)
        if (array_search(6, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 6])->andWhere(['teacherParticipantBranches.branch_id' => $this->branch])->all();
            
            $e2 = [];
            foreach ($events3 as $event) $e2[] = $event->id;

            $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'foreign_event_id', $e2])->all();


            $eIds2 = [];
            foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

            $counter5 = 0;
            $counter6 = 0;
            $counterPart1 = 0;
            $allTeams = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= $event->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                $counterTeam = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team;
                }

                $tpIds = [];
                foreach ($tIds as $tId)
                    $tpIds[] = $tId->participant_id;

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $counter5 += count($achieves1) + $counterTeamPrizes;
                $counter6 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam;
                $allTeams += $counterTeam;

                //ОТЛАДКА
                $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= (count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam).$teamStr.";".$s1.$teamPrizeStr.";".$s2. $teamWinnersStr."\r\n";
                //ОТЛАДКА
            }

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($counterPart1 !== 0)
            {
                $r1 = ($counter5 * 1.0) / ($counterPart1 * 1.0);
                $r2 = ($counter6 * 1.0) / ($counterPart1 * 1.0);
                $r3 = (($counter5 + $counter6) * 1.0) / ($counterPart1 * 1.0);

                $bigCounter += $counterPart1;
                $bigPrizes += $counter5 + $counter6;
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками региональных конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".$counter5."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".$counter6."</td></tr>";

            //if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            //if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            //if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами региональных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";

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

            $events3 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 5])->andWhere(['teacherParticipantBranches.branch_id' => $this->branch])->all();

            $e2 = [];
            foreach ($events3 as $event) $e2[] = $event->id;

            $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'foreign_event_id', $e2])->all();


            $eIds2 = [];
            foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

            $counter7 = 0;
            $counter8 = 0;
            $counterPart1 = 0;
            $allTeams = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= $event->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                $counterTeam = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team;
                }

                $tpIds = [];
                foreach ($tIds as $tId)
                    $tpIds[] = $tId->participant_id;

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $counter7 += count($achieves1) + $counterTeamPrizes;
                $counter8 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam;
                $allTeams += $counterTeam;

                //ОТЛАДКА
                $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= (count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam).$teamStr.";".$s1.$teamPrizeStr.";".$s2. $teamWinnersStr."\r\n";
                //ОТЛАДКА
            }

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($counterPart1 !== 0)
            {
                $r1 = ($counter7 * 1.0) / ($counterPart1 * 1.0);
                $r2 = ($counter8 * 1.0) / ($counterPart1 * 1.0);
                $r3 = (($counter7 + $counter8) * 1.0) / ($counterPart1 * 1.0);
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками городских конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами городских конкурсных мероприятий</td><td>".$counter7."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями городских конкурсных мероприятий</td><td>".$counter8."</td></tr>";

            //if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами городских конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            //if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями городских конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            //if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами городских конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (районных)
        if (array_search(4, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 4])->andWhere(['teacherParticipantBranches.branch_id' => $this->branch])->all();

            $e2 = [];
            foreach ($events3 as $event) $e2[] = $event->id;

            $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'foreign_event_id', $e2])->all();


            $eIds2 = [];
            foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

            $counter9 = 0;
            $counter10 = 0;
            $counterPart1 = 0;
            $allTeams = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= $event->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                $counterTeam = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team;
                }

                $tpIds = [];
                foreach ($tIds as $tId)
                    $tpIds[] = $tId->participant_id;

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $counter9 += count($achieves1) + $counterTeamPrizes;
                $counter10 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam;
                $allTeams += $counterTeam;

                //ОТЛАДКА
                $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= (count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam).$teamStr.";".$s1.$teamPrizeStr.";".$s2. $teamWinnersStr."\r\n";
                //ОТЛАДКА
            }

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($counterPart1 !== 0)
            {
                $r1 = ($counter9 * 1.0) / ($counterPart1 * 1.0);
                $r2 = ($counter10 * 1.0) / ($counterPart1 * 1.0);
                $r3 = (($counter9 + $counter10) * 1.0) / ($counterPart1 * 1.0);
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками районных конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами районных конкурсных мероприятий</td><td>".$counter9."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями районных конкурсных мероприятий</td><td>".$counter10."</td></tr>";

            //if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами районных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            //if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями районных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            //if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами районных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (внутренние)
        if (array_search(3, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 3])->andWhere(['teacherParticipantBranches.branch_id' => $this->branch])->all();

            $e2 = [];
            foreach ($events3 as $event) $e2[] = $event->id;

            $eventParticipants = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'focus', $this->focus])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->andWhere(['IN', 'foreign_event_id', $e2])->all();


            $eIds2 = [];
            foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

            $counter11 = 0;
            $counter12 = 0;
            $counterPart1 = 0;
            $allTeams = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= $event->name.";".$event->eventLevel->name.";".$event->start_date.";".$event->finish_date.";";
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                $counterTeam = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team;
                }

                $tpIds = [];
                foreach ($tIds as $tId)
                    $tpIds[] = $tId->participant_id;

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $counter11 += count($achieves1) + $counterTeamPrizes;
                $counter12 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam;
                $allTeams += $counterTeam;

                //ОТЛАДКА
                $teams = TeamWork::find()->select('name')->distinct()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $eIds2])->all();
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= (count(TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['IN', 'teacherParticipantBranches.branch_id', $this->branch])->andWhere(['IN', 'allow_remote_id', $this->allow_remote])->all()) + $counterTeam).$teamStr.";".$s1.$teamPrizeStr.";".$s2. $teamWinnersStr."\r\n";
                //ОТЛАДКА
            }

            $r1 = 0;
            $r2 = 0;
            $r3 = 0;
            if ($counterPart1 !== 0)
            {
                $r1 = ($counter11 * 1.0) / ($counterPart1 * 1.0);
                $r2 = ($counter12 * 1.0) / ($counterPart1 * 1.0);
                $r3 = (($counter11 + $counter12) * 1.0) / ($counterPart1 * 1.0);
            }

            $addStr = $allTeams > 0 ? ' (в т.ч. команд - '.$allTeams.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками внутренних конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами внутренних конкурсных мероприятий</td><td>".$counter11."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями внутренних конкурсных мероприятий</td><td>".$counter12."</td></tr>";

            //if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами внутренних конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            //if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями внутренних конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            //if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами внутренних конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";



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