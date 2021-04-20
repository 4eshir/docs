<?php


namespace app\models\extended;


use app\models\common\LessonTheme;
use app\models\common\Visit;

class JournalModel extends \yii\base\Model
{
    public $visits; //матрица посещений
    public $visits1; //матрица посещений
    public $trainingGroup; //группа
    public $participants; //список учеников
    public $lessons; //список занятий
    public $themes; //список тем занятий
    public $teachers; //список педагогов, ведущих занятия
    public $visits_id; //id записей о посещении

    function __construct($group_id = null)
    {
        $this->trainingGroup = $group_id;
    }

    public function rules()
    {
        return [
            [['visits', 'participants', 'lessons', 'themes', 'teachers', 'visits_id'], 'safe'],
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
        for ($i = 0; $i !== count($this->visits); $i++, $k++)
        {
            $vis = Visit::find()->where(['id' => $this->visits_id[$i]])->one();
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
                $theme->save();
            }
        }
    }
}