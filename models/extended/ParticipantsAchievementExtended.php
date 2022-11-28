<?php


namespace app\models\extended;


class ParticipantsAchievementExtended extends \yii\base\Model
{
    public $fio;
    public $achieve;
    public $winner;

    public function rules()
    {
        return [
            [['fio', 'achieve'], 'string'],
            [['winner'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО участника',
            'achieve' => 'Достижение',
            'winner' => 'Победитель',
        ];
    }
}