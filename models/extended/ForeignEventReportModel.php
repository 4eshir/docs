<?php


namespace app\models\extended;


use app\models\common\TrainingGroup;
use app\models\work\ForeignEventWork;
use app\models\work\LessonThemeWork;
use app\models\work\PeopleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantWork;
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

        $events = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->all();

        //-------------------------------------------

        //======РЕЗУЛЬТАТ======
        $resultHTML = "<table class='table table-bordered'><tr><td><b>Описание параметра</b></td><td><b>Значение</b></td></tr>";
        //Вывод ВСЕХ обучающихся (по группам)
        $resultHTML .= "<tr><td>Общее число обучающихся</td><td>".count($participants)."</td></tr>";
        //-----------------------------------
        //=====================
        $resultHTML .= "</table>";

        return $resultHTML;
    }

    public function getAge($birthdate, $target_date)
    {
        $bdTime = new DateTime($birthdate);
        $tdTime = new DateTime($target_date);
        $interval = $tdTime->diff($bdTime);
        var_dump($interval->y);
        return $interval->y;
    }

    public function save()
    {
        return true;
    }
}