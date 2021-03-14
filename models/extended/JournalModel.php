<?php


namespace app\models\extended;


use app\models\common\Visit;

class JournalModel extends \yii\base\Model
{
    public $visits; //матрица посещений
    public $trainingGroup; //группа
    public $participants; //список учеников
    public $lessons; //список занятий

    function __construct($group_id = null)
    {
        $this->trainingGroup = $group_id;
    }

    public function rules()
    {
        return [
            [['visits', 'participants', 'lessons'], 'safe'],
            [['trainingGroup'], 'integer'],
        ];
    }

    public function ClearVisits()
    {
        $size = count($this->visits);
        for ($i = 0; $i != $size; $i++)
            if ($this->visits[$i] == 1)
                unset($this->visits[$i - 1]);
        $this->visits = array_values($this->visits);
    }

    public function save()
    {
        $j = 0;
        $k = 0;
        for ($i = 0; $i !== count($this->visits); $i++, $k++)
        {
            if ($i % count($this->lessons) == 0 && $i !== 0)
            {
                $j++;
                $k = 0;
            }
            $vis = new Visit();
            $vis->foreign_event_participant_id = $this->participants[$j];
            $vis->training_group_lesson_id = $this->lessons[$k];
            $vis->status = $this->visits[$i];
            $vis->save(false);
        }
    }
}