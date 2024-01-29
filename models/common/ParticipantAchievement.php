<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "participant_achievement".
 *
 * @property int $id
 * @property int $teacher_participant_id
 * @property int $participant_id
 * @property int $foreign_event_id
 * @property string $achievment
 * @property int $winner
 * @property string|null $cert_number
 * @property string|null $nomination
 * @property string|null $date
 * @property int|null $team_name_id
 *
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property TeacherParticipant $teacherParticipant
 * @property TeamName $teamName
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
            [['id', 'participant_id', 'foreign_event_id', 'achievment'], 'required'],
            [['id', 'teacher_participant_id', 'participant_id', 'foreign_event_id', 'winner', 'team_name_id'], 'integer'],
            [['date'], 'safe'],
            [['achievment', 'cert_number', 'nomination'], 'string', 'max' => 1000],
            [['id'], 'unique'],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['teacher_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherParticipant::className(), 'targetAttribute' => ['teacher_participant_id' => 'id']],
            [['team_name_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeamName::className(), 'targetAttribute' => ['team_name_id' => 'id']],
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
            'participant_id' => 'Participant ID',
            'foreign_event_id' => 'Foreign Event ID',
            'achievment' => 'Achievment',
            'winner' => 'Winner',
            'cert_number' => 'Cert Number',
            'nomination' => 'Nomination',
            'date' => 'Date',
            'team_name_id' => 'Team Name ID',
        ];
    }

    /**
     * Gets query for [[ForeignEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvent()
    {
        return $this->hasOne(ForeignEvent::className(), ['id' => 'foreign_event_id']);
    }

    /**
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::className(), ['id' => 'participant_id']);
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

    /**
     * Gets query for [[TeamName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeamName()
    {
        return $this->hasOne(TeamName::className(), ['id' => 'team_name_id']);
    }
}
