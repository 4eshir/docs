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
use Mpdf\Tag\P;
use yii\db\Query;

class ForeignEventReportModel extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $branch;
    public $focus;
    public $sex;
    public $age_left;
    public $age_right;


    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['sex', 'focus', 'branch'], 'safe'],
            [['age_left', 'age_right'], 'integer']
        ];
    }

    public function generateReport()
    {
        $events = ForeignEventWork::find()->where(['start_date' >= $this->start_date])->andWhere(['finish_date' <= $this->finish_date]);
        $teachers = PeopleWork::find()->where(['IN', 'branch_id', $this->branch])->all();
        $tIds = [];
        foreach ($teachers as $teacher) $tIds[] = $teacher->id;

        $event_teacher = TeacherParticipantWork::find()->where(['IN', 'teacher_id', $tIds])->orWhere(['IN', 'teacher2_id', $tIds])->all();
        $eIds = [];
        foreach ($event_teacher as $event) $eIds[] = $event->foreign_event_id;

        $events = $events->andWhere(['IN', 'id', $eIds]);
    }

    public function save()
    {
        return true;
    }
}