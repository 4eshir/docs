<?php


namespace app\models\extended;

use app\models\common\Feedback;
use yii\validators\EachValidator;
use yii\base\Model;

class FeedbackAnswer extends Model
{
    public $answer = [];
    public $id = [];
    public $type;

    public function rules()
    {
        return [
          ['answer', 'safe'],
          ['id', 'safe'],
        ];
    }

    public function loadFeedback()
    {
        for ($i = 0; $i != count($this->answer); $i++)
        {
            $feedback = Feedback::find()->where(['id' => $this->id[$i]])->one();
            $feedback->answer = $this->answer[$i];
            $feedback->save();
        }
    }
}