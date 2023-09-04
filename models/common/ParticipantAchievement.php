<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "participant_achievement".
 *
 * @property int $id
 * @property int|null $participant_id
 * @property int|null $foreign_event_id
 * @property int $teacher_participant_id
 * @property string $achievment
 * @property int $winner
 * @property string|null $cert_number
 * @property string|null $nomination
 * @property string|null $date
 *
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property int|null $team_name_id
 *
 * @property TeamName $teamName
 * @property TeacherParticipant $teacherParticipant
 */
class ParticipantAchievement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participant_achievement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_participant_id', 'achievment'], 'required'],
            [['participant_id', 'foreign_event_id', 'teacher_participant_id', 'winner'], 'integer'],
            [['date'], 'safe'],
            [['achievment', 'cert_number', 'nomination'], 'string', 'max' => 1000],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_participant_id' => 'Teacher Participant ID',
            'achievment' => 'Achievment',
            'winner' => 'Winner',
            'cert_number' => 'Cert Number',
            'nomination' => 'Nomination',
            'date' => 'Date',
            'team_name_id' => 'Team Name ID',
        ];
    }

    /**
     * Gets query for [[TeamName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeamName()
    {
        return $this->hasOne(TeamName::className(), ['id' => 'team_name_id']);
    }


    /**
     * Gets query for [[TeacherParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipant()
    {
        return $this->hasOne(TeacherParticipant::className(), ['id' => 'teacher_participant_id']);
    }
}
