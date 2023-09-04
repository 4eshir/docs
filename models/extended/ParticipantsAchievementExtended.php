<?php


namespace app\models\extended;


class ParticipantsAchievementExtended extends \yii\base\Model
{
    public $fio;
    public $achieve;
    public $winner;
    public $cert_number;
    public $nomination;
    public $date;
    public $fioString;

    public function rules()
    {
        return [
            [['fio', 'achieve', 'cert_number', 'nomination'], 'string'],
            [['winner'], 'integer'],
            [['date', 'fioString'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО участника',
            'achieve' => 'Достижение',
            'winner' => 'Победитель',
            'cert_number' => 'Номер наградного документа',
            'nomination' => 'Номинация',
            'date' => 'Дата наградного документа',
        ];
    }
}