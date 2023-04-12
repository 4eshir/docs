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

    public function rules()
    {
        return [
            [['fio', 'achieve', 'cert_number', 'nomination'], 'string'],
            [['winner'], 'integer'],
            ['date', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО участника',
            'achieve' => 'Достижение',
            'winner' => 'Победитель',
            'cert_number' => 'Номер сертификата',
            'nomination' => 'Номинация',
            'date' => 'Дата выдачи сертификата',
        ];
    }
}