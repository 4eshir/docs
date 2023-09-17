<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "participant_files".
 *
 * @property int $id
 * @property int $teacher_participant_id
 * @property int|null $participant_id
 * @property int|null $foreign_event_id
 * @property string|null $filename
 *
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property TeacherParticipant $teacherParticipant
 */
class ParticipantFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participant_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_participant_id', 'participant_id', 'foreign_event_id'], 'integer'],
            [['teacher_participant_id'], 'required'],
            [['filename'], 'string', 'max' => 1000],
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
            'teacher_participant_id' => 'Teacher Participant ID',
            'participant_id' => 'Participant ID',
            'foreign_event_id' => 'Foreign Event ID',
            'filename' => 'Filename',
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
}
