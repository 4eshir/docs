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
        $debug = '<table class="table table-bordered"><tr><td><b>Мероприятие</b></td><td><b>Уровень</b></td><td><b>Дата начала</b></td><td><b>Дата окончания</b></td><td>Призеры</td><td>Победители</td></tr>';
        //ОТЛАДКА

        //Получаем группы и учеников

        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
            ->where(['<=', 'start_date', $this->end_date])->andWhere(['>=', 'finish_date', $this->end_date])
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
        $eventParticipants = TeacherParticipantWork::find()->where(['IN', 'participant_id', $pIds])->all();

        $eIds = [];
        foreach ($eventParticipants as $eventParticipant) $eIds[] = $eventParticipant->foreign_event_id;

        $events = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date]);


        //-------------------------------------------

        //======РЕЗУЛЬТАТ======
        $resultHTML = "<table class='table table-bordered'><tr><td><b>Наименование показателя</b></td><td><b>Значение показателя</b></td></tr>";
        //Вывод ВСЕХ обучающихся (по группам)
        $resultHTML .= "<tr><td>Общее число обучающихся</td><td>".count($participants)."</td></tr>";
        //-----------------------------------
        //Вывод количества призеров / победителей (международных)
        if (array_search(8, $this->level) !== null)
        {
            $events1 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 8])->all();

            $counter1 = 0;
            $counter2 = 0;
            $counterPart1 = 0;
            foreach ($events1 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td>'.$event->start_date.'</td><td>'.$event->finish_date.'</td>';
                //ОТЛАДКА
                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter1 += count($achieves1) + $counterTeamPrizes;
                $counter2 += count($achieves2) + $counterTeamWinners;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $debug .= '<td>'.$counter1.' (в т.ч. команды - '.$counterTeamPrizes.')</td><td>'.$counter2. '(в т.ч. команды - '.$counterTeamWinners.')</td></tr>';
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

            $resultHTML .= "<tr><td>Число учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".$counter1."</td></tr>";
            $resultHTML .= "<tr><td>Число учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".$counter2."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами международных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями международных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами международных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";
        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (всероссийских)
        if (array_search(7, $this->level) !== null)
        {
            $events2 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 7])->all();


            $counter3 = 0;
            $counter4 = 0;
            $counterPart1 = 0;
            foreach ($events2 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td>'.$event->start_date.'</td><td>'.$event->finish_date.'</td>';
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter3 += count($achieves1) + $counterTeamPrizes;
                $counter4 += count($achieves2) + $counterTeamWinners;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $debug .= '<td>'.$counter3.' (в т.ч. команды - '.$counterTeamPrizes.')</td><td>'.$counter4. '(в т.ч. команды - '.$counterTeamWinners.')</td></tr>';
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

            $resultHTML .= "<tr><td>Число учащихся, являющихся призерами всероссийских конкурсных мероприятий</td><td>".$counter3."</td></tr>";
            $resultHTML .= "<tr><td>Число учащихся, являющихся победителями всероссийских конкурсных мероприятий</td><td>".$counter4."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами всероссийских конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями всероссийских конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами всероссийских конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


        }
        //-----------------------------------------
        //Вывод количества призеров / победителей (региональных)
        if (array_search(6, $this->level) !== null)
        {

            $events3 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->andWhere(['event_level_id' => 6])->all();

            $counter5 = 0;
            $counter6 = 0;
            $counterPart1 = 0;
            foreach ($events3 as $event)
            {
                //ОТЛАДКА
                $debug .= '<tr>';
                $debug .= '<td>'.$event->name.'</td><td>'.$event->eventLevel->name.'</td><td>'.$event->start_date.'</td><td>'.$event->finish_date.'</td>';
                //ОТЛАДКА

                $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();
                $tIds = [];
                $teamName = '';
                $counterTeamWinners = 0;
                $counterTeamPrizes = 0;
                foreach ($teams as $team)
                {
                    if ($teamName != $team->name)
                    {
                        $teamName = $team->name;
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                        if ($res !== null) $counterTeamWinners++;
                        else $counterTeamPrizes++;
                    }
                    $tIds[] = $team->participant_id;
                }
                $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 0])->all();
                $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tIds])->andWhere(['winner' => 1])->all();
                $counter5 += count($achieves1) + $counterTeamPrizes;
                $counter6 += count($achieves2) + $counterTeamPrizes;
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->all());

                //ОТЛАДКА
                $debug .= '<td>'.$counter5.' (в т.ч. команды - '.$counterTeamPrizes.')</td><td>'.$counter6. '(в т.ч. команды - '.$counterTeamWinners.')</td></tr>';
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
            $resultHTML .= "<tr><td>Число учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".$counter5."</td></tr>";
            $resultHTML .= "<tr><td>Число учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".$counter6."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся призерами региональных конкурсных мероприятий</td><td>".round($r1, 2)."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями региональных конкурсных мероприятий</td><td>".round($r2, 2)."</td></tr>";
            $resultHTML .= "<tr><td>Доля учащихся, являющихся победителями и призерами региональных конкурсных мероприятий</td><td>".round($r3, 2)."</td></tr>";


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