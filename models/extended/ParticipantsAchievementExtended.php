<?php


namespace app\models\extended;


class ParticipantsAchievementExtended extends \yii\base\Model
{
    public $fio;
    public $achieve;

    public function rules()
    {
        return [
            [['fio', 'achieve'], 'string']
        ];
    }
}