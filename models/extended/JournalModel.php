<?php


namespace app\models\extended;


use app\models\common\LessonTheme;
use app\models\common\Visit;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\ProjectThemeWork;
use app\models\work\GroupProjectThemesWork;
use app\models\components\Logger;
use app\models\work\TrainingGroupWork;
use Yii;

class JournalModel extends \yii\base\Model
{
    public $visits; //матрица посещений
    public $visits1; //матрица посещений
    public $trainingGroup; //группа
    public $participants; //список учеников
    public $lessons; //список занятий
    public $themes; //список тем занятий
    public $teachers; //список педагогов, ведущих занятия
    public $controls; //список форм контроля
    public $visits_id; //id записей о посещении

    public $projectThemes; //темы проектов
    public $cwPoints; //оценки
    public $successes; //успешное завершение
    public $tpIds; //айдишники group_participant

    public $groupProjectThemes;

    function __construct($group_id = null)
    {
        $this->trainingGroup = $group_id;
    }

    public function rules()
    {
        return [
            [['visits', 'participants', 'lessons', 'themes', 'teachers', 'visits_id', 'controls', 'projectThemes', 'cwPoints', 'successes', 'tpIds', 'groupProjectThemes'], 'safe'],
            [['trainingGroup'], 'integer'],
        ];
    }

    public function ClearVisits()
    {
        if ($this->visits !== null) {
            $size = count($this->visits);
            for ($i = 0; $i != $size; $i++)
                if ($this->visits[$i] == 1)
                    unset($this->visits[$i - 1]);
            $this->visits = array_values($this->visits);
        }
    }

    public function save()
    {
        $j = 0;
        $k = 0;
        $strVis = "";
        for ($i = 0; $i !== count($this->visits); $i++, $k++)
        {
            $vis = Visit::find()->where(['id' => $this->visits_id[$i]])->one();
            if ($vis->status != $this->visits[$i])
                $strVis .= $this->visits_id[$i].': '.$this->visits[$i].', ';
            $vis->status = $this->visits[$i];
            $vis->save(false);
            /*if ($i % count($this->lessons) == 0 && $i !== 0)
            {
                $j++;
                $k = 0;
            }
            $vis = Visit::find()->where(['training_group_lesson_id' => $this->lessons[$k]])->andWhere(['foreign_event_participant_id' => $this->participants[$j]])->one();
            if ($vis == null)
                $vis = new Visit();
            $vis->foreign_event_participant_id = $this->participants[$j];
            $vis->training_group_lesson_id = $this->lessons[$k];
            $vis->status = $this->visits[$i];
            $vis->save(false);*/
        }
        $strVis = substr($strVis,0,-2);

        $group = TrainingGroupWork::find()->where(['id' => $this->trainingGroup])->one();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменены явки в журнале группы '.$group->number.'. Visit('.$strVis.')');

        if ($this->themes !== null)
        {
            for ($i = 0; $i !== count($this->themes); $i++)
            {
                $theme = LessonTheme::find()->where(['training_group_lesson_id' => $this->lessons[$i]])->one();
                if ($theme == null)
                    $theme = new LessonTheme();
                if (strlen($this->themes[$i]) > 0)
                {
                    $theme->theme = $this->themes[$i];
                    $theme->training_group_lesson_id = $this->lessons[$i];
                    $theme->teacher_id = $this->teachers[$i];
                    $theme->control_type_id = $this->controls[$i];
                    $theme->save();
                }
            }
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен тематический план группы '.$group->number);
        }

        $tempSuccess = [];
        if ($this->successes !== null)
        {
            for ($i = 0; $i < count($this->successes) - 1; $i++)
            if ($this->successes[$i] == 0 && $this->successes[$i + 1] != 0)
                $tempSuccess[] = $this->successes[$i + 1];
            else if ($this->successes[$i] == 0)
                $tempSuccess[] = $this->successes[$i];
        }

        for ($i = 0; $i < count($this->projectThemes) - 1; $i++)
            $this->projectThemes[$i] = $this->projectThemes[$i + 1];

        if ($this->tpIds !== null)
            for ($i = 0; $i < count($this->tpIds); $i++)
            {
                $tp = TrainingGroupParticipantWork::find()->where(['id' => $this->tpIds[$i]])->one();
                $tp->points = $this->cwPoints[$i];
                if ($this->tpIds[$i] == $tempSuccess[$i]) $tp->success = 1;
                else $tp->success = false;
                $tp->group_project_themes_id = $this->projectThemes[$i];

                //echo $tp->participantWork->shortName.' '.$this->projectThemes[$i].'<br>';

                $tp->save();
            }

    }
}