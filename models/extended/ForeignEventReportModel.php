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


    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['focus', 'branch', 'budget', 'prize', 'level'], 'safe'],

        ];
    }

    public function generateReport()
    {
        //ОТЛАДКА
        $debug = '<table class="table table-bordered" style="font-size: 14px"><tr><td><b>Мероприятие</b></td><td><b>Уровень</b></td><td><b>Дата начала</b></td><td><b>Дата окончания</b></td><td><b>Кол-во участников</b></td><td><b>Призеры</b></td><td><b>Победители</b></td></tr>';
        //ОТЛАДКА

        //Получаем группы и учеников

        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
            ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
            ->andWhere(['IN', 'budget', $this->budget])
            ->all();

        $tgIds = [];
        foreach ($trainingGroups as $trainingGroup) $tgIds[] = $trainingGroup->id;
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->all();

        //--------------------------

        //Получаем мероприятия с выбранными учениками

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;
        $eventParticipants = TeacherParticipantWork::find()->where(['IN', 'participant_id', $pIds])->andWhere(['IN', 'branch_id', $this->branch])->all();

        $eIds = [];
        foreach ($eventParticipants as $eventParticipant) $eIds[] = $eventParticipant->foreign_event_id;

        $events = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date]);


        //-------------------------------------------

        //======РЕЗУЛЬТАТ======
        $resultHTML = "<table class='table table-bordered'><tr><td><b>Наименование показателя</b></td><td><b>Значение показателя</b></td></tr>";
        //Вывод ВСЕХ обучающихся (по группам)
        $resultHTML .= "<tr><td>Общее число обучающихся</td><td>".count($participants)."</td></tr>";
        //-----------------------------------
        $counterTeam = 0;
        //Вывод количества призеров / победителей (международных)
        if (array_search(8, $this->level) !== false)
        {
            $events1 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 8])->all();

            $counter1 = 0;
            $counter2 = 0;
            $counterPart1 = 0;
            foreach ($events1 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td nowrap>'.$event->start_date.'</td><td nowrap>'.$event->finish_date.'</td>';
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
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team->participant_id;
                }

                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter1 += count($achieves1) + $counterTeamPrizes;
                $counter2 += count($achieves2) + $counterTeamWinners;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= '<td>'.count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all()).$teamStr.'</td><td>'.$s1.$teamPrizeStr.'</td><td>'.$s2. $teamWinnersStr.'</td></tr>';
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
            }

            $addStr = $counterTeam > 0 ? ' (в т.ч. команд - '.$counterTeam.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками международных конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".$counter1."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".$counter2."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами международных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";
        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (всероссийских)
        if (array_search(7, $this->level) !== false)
        {
            $events2 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 7])->all();


            $counter3 = 0;
            $counter4 = 0;
            $counterPart1 = 0;
            foreach ($events2 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td nowrap>'.$event->start_date.'</td><td nowrap>'.$event->finish_date.'</td>';
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
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter3 += count($achieves1) + $counterTeamPrizes;
                $counter4 += count($achieves2) + $counterTeamWinners;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= '<td>'.count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all()).$teamStr.'</td><td>'.$s1.$teamPrizeStr.'</td><td>'.$s2. $teamWinnersStr.'</td></tr>';
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
            }

            $addStr = $counterTeam > 0 ? ' (в т.ч. команд - '.$counterTeam.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками всероссийских конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами всероссийских конкурсных мероприятий</td><td>".$counter3."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями всероссийских конкурсных мероприятий</td><td>".$counter4."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами всероссийских конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями всероссийских конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами всероссийских конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (региональных)
        if (array_search(6, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 6])->all();

            $counter5 = 0;
            $counter6 = 0;
            $counterPart1 = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td nowrap>'.$event->start_date.'</td><td nowrap>'.$event->finish_date.'</td>';
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
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter5 += count($achieves1) + $counterTeamPrizes;
                $counter6 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= '<td>'.count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all()).$teamStr.'</td><td>'.$s1.$teamPrizeStr.'</td><td>'.$s2. $teamWinnersStr.'</td></tr>';
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
            }

            $addStr = $counterTeam > 0 ? ' (в т.ч. команд - '.$counterTeam.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками региональных конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".$counter5."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".$counter6."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами региональных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (городских)
        if (array_search(5, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 5])->all();

            $counter7 = 0;
            $counter8 = 0;
            $counterPart1 = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td nowrap>'.$event->start_date.'</td><td nowrap>'.$event->finish_date.'</td>';
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
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter7 += count($achieves1) + $counterTeamPrizes;
                $counter8 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= '<td>'.count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all()).$teamStr.'</td><td>'.$s1.$teamPrizeStr.'</td><td>'.$s2. $teamWinnersStr.'</td></tr>';
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

            $addStr = $counterTeam > 0 ? ' (в т.ч. команд - '.$counterTeam.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками городских конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами городских конкурсных мероприятий</td><td>".$counter7."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями городских конкурсных мероприятий</td><td>".$counter8."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами городских конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями городских конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами городских конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (районных)
        if (array_search(4, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 4])->all();

            $counter9 = 0;
            $counter10 = 0;
            $counterPart1 = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td nowrap>'.$event->start_date.'</td><td nowrap>'.$event->finish_date.'</td>';
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
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter9 += count($achieves1) + $counterTeamPrizes;
                $counter10 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= '<td>'.count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all()).$teamStr.'</td><td>'.$s1.$teamPrizeStr.'</td><td>'.$s2. $teamWinnersStr.'</td></tr>';
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

            $addStr = $counterTeam > 0 ? ' (в т.ч. команд - '.$counterTeam.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками районных конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами районных конкурсных мероприятий</td><td>".$counter9."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями районных конкурсных мероприятий</td><td>".$counter10."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами районных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями районных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами районных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (внутренние)
        if (array_search(3, $this->level) !== false)
        {

            $events3 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 3])->all();

            $counter11 = 0;
            $counter12 = 0;
            $counterPart1 = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td nowrap>'.$event->start_date.'</td><td nowrap>'.$event->finish_date.'</td>';
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
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                        if ($res !== null) $counterTeam++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter11 += count($achieves1) + $counterTeamPrizes;
                $counter12 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $s1 = count($achieves1) + $counterTeamPrizes;
                $s2 = count($achieves2) + $counterTeamWinners;
                $teamStr = count($teams) > 0 ? ' (в т.ч. команды - '.count($teams).')' : '';
                $teamPrizeStr = $counterTeamPrizes > 0 ? ' (в т.ч. команды - '.$counterTeamPrizes.')' : '';
                $teamWinnersStr = $counterTeamWinners > 0 ? ' (в т.ч. команды - '.$counterTeamWinners.')' : '';
                $debug .= '<td>'.count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all()).$teamStr.'</td><td>'.$s1.$teamPrizeStr.'</td><td>'.$s2. $teamWinnersStr.'</td></tr>';
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

            $addStr = $counterTeam > 0 ? ' (в т.ч. команд - '.$counterTeam.')' : '';

            $resultHTML .= "<tr><td>Число учащихся, являющихся участниками внутренних конкурсных мероприятий</td><td>".$counterPart1.$addStr."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся призерами внутренних конкурсных мероприятий</td><td>".$counter11."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Число учащихся, являющихся победителями внутренних конкурсных мероприятий</td><td>".$counter12."</td></tr>";
            if (array_search(0, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами внутренних конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            if (array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями внутренних конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            if (array_search(0, $this->prize) !== false && array_search(1, $this->prize) !== false) $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами внутренних конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //=====================
        $resultHTML .= "</table>";
        $debug .= '</table>';
        return [$resultHTML, $debug];
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