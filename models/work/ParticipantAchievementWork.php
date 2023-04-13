<?php

namespace app\models\work;

use app\models\common\ParticipantAchievement;
use Yii;


class ParticipantAchievementWork extends ParticipantAchievement
{
    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО участника',
            'achievment' => 'Достижение',
            'winner' => 'Победитель',
            'cert_number' => 'Номер наградного документа',
            'nomination' => 'Номинация',
            'date' => 'Дата наградного документа',
        ];
    }

    public function getParticipantWork()
    {
        return $this->hasOne(ForeignEventParticipantsWork::className(), ['id' => 'participant_id']);
    }
}
