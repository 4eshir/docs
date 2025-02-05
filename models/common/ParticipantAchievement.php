<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "participant_achievement".
 *
 * @property int $id
 * @property int $teacher_participant_id
 * @property int|null $participant_id
 * @property int|null $foreign_event_id
 * @property string $achievment
 * @property int $winner
 * @property string|null $cert_number
 * @property string|null $nomination
 * @property string|null $date
 * @property int|null $team_name_id
 *
 * @property TeacherParticipant $teacherParticipant
 * @property ForeignEvent $foreignEvent
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
            [['teacher_participant_id', 'participant_id', 'foreign_event_id', 'winner', 'team_name_id'], 'integer'],
            [['achievment'], 'required'],
            [['date'], 'safe'],
            [['achievment', 'cert_number', 'nomination'], 'string', 'max' => 1000],
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
     * Gets query for [[TeacherParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipant()
    {
        return $this->hasOne(TeacherParticipant::className(), ['id' => 'teacher_participant_id']);
    }

    public function getForeignEvent()
    {
        return $this->hasOne(ForeignEvent::className(), ['id' => 'foreign_event_id']);
    }
}
