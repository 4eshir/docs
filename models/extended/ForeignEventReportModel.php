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
        $events = ForeignEventWork::find()->where(['finish_date' >= $this->start_date])->andWhere(['finish_date' <= $this->end_date]);
        $teachers = PeopleWork::find()->where(['IN', 'branch_id', $this->branch])->all();
        $tIds = [];
        foreach ($teachers as $teacher) $tIds[] = $teacher->id;

        $event_teacher = TeacherParticipantWork::find()->where(['IN', 'teacher_id', $tIds])->orWhere(['IN', 'teacher2_id', $tIds])->all();
        $eIds = [];
        foreach ($event_teacher as $event) $eIds[] = $event->foreign_event_id;

        $events = $events->andWhere(['IN', 'id', $eIds]); //отбор по отделу (педагогов)

        /*
         * Здесь должен быть
         * отбор по направленности
         */

        $age = [];
        for ($i = $this->age_left + 0; $i <= $this->age_right; $i++) $age[] = $i;
        $this->getAge('2021-11-24', '1999-01-10');
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