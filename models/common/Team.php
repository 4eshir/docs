<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property string|null $name
 * @property int $teacher_participant_id
 * @property int|null $foreign_event_id
 * @property int|null $participant_id
 * @property int|null $team_name_id
 *
 * @property TeamName $teamName
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property TeacherParticipant $teacherParticipant
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_participant_id', 'foreign_event_id', 'participant_id', 'team_name_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['team_name_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeamName::className(), 'targetAttribute' => ['team_name_id' => 'id']],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['teacher_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherParticipant::className(), 'targetAttribute' => ['teacher_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'teacher_participant_id' => 'Teacher Participant ID',
            'foreign_event_id' => 'Foreign Event ID',
            'participant_id' => 'Participant ID',
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
}
