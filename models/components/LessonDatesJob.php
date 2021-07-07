<?php


namespace app\models\components;


use app\models\common\TrainingGroupLesson;
use app\models\work\TrainingGroupLessonWork;
use yii\base\BaseObject;
use yii\queue\Queue;

class LessonDatesJob extends BaseObject implements \yii\queue\JobInterface
{
    public $trainingGroupId;
    public $partsFinal;

    public function execute($queue)
    {
        $this->partsFinal = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->trainingGroupId])->orderBy(['lesson_date' => SORT_ASC])->all();
        // TODO: Implement execute() method.
    }
}