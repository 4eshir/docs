<?php

namespace app\models\work;

use app\models\common\ForeignEvent;
use app\models\common\ParticipantAchievement;
use app\models\null\ForeignEventParticipantsNull;
use app\models\null\TeacherParticipantNull;
use Yii;

/**
 * @property TeacherParticipantWork $teacherParticipantWork
 * @property ForeignEventWork $foreignEventWork
 */
class ParticipantAchievementWork extends ParticipantAchievement
{
    const PRIZE = 0;
    const WINNER = 1;
    const ALL = [0, 1];

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

    public function getForeignEventWork()
    {
        return $this->hasOne(ForeignEventWork::className(), ['id' => 'foreign_event_id']);
    }

    public function getParticipantWork()
    {
        $try = $this->hasOne(ForeignEventParticipantsWork::className(), ['id' => 'participant_id']);
        return $try->all() ? $try : new ForeignEventParticipantsNull();
    }

    public function getTeacherParticipantWork()
    {
        return $this->hasOne(TeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
    }

    public function getStatusString()
    {
        return $this->winner == 1 ? 'Победитель' : 'Призер';
    }

    public function getActParticipationString()
    {
        $part = TeacherParticipantWork::find()->where(['id' => $this->teacher_participant_id])->one();
        $result = 'Номинация: '. $part->nomination . '. Направленность: ' . $part->focus0->name . '. ';

        if ($part->teamNameString == null)
            $result .= 'Индивидуальное участие';
        else
            $result .= 'В составе команды "' . $part->teamNameString . '"';
        return $result;
    }
}
