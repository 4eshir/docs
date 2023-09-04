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

class ReportFormModel extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $branch;
    public $focus;
    public $budget;
    public $prize;
    public $level;
    public $method;
    public $year;


    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['year'], 'integer'],
            [['focus', 'branch', 'budget', 'prize', 'level', 'method'], 'safe'],

        ];
    }

   public function save()
    {
        return true;
    }
}