<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "teacher_participant".
 *
 * @property int $id
 * @property int $participant_id
 * @property int $teacher_id
 * @property int|null $teacher2_id
 * @property int $foreign_event_id
 * @property string $focus
 *
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property People $teacher
 * @property People $teacher2
 * @property TeacherParticipantBranch[] $teacherParticipantBranches
 */
class TeacherParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'teacher_id', 'foreign_event_id', 'focus'], 'required'],
            [['participant_id', 'teacher_id', 'teacher2_id', 'foreign_event_id'], 'integer'],
            [['focus'], 'string', 'max' => 1000],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['teacher2_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher2_id' => 'id']],
        ];
    }

    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participant_id' => 'Participant ID',
            'teacher_id' => 'Teacher ID',
            'teacher2_id' => 'Teacher2 ID',
            'foreign_event_id' => 'Foreign Event ID',
            'focus' => 'Focus',
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
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher_id']);
    }

    /**
     * Gets query for [[Teacher2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher2()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher2_id']);
    }

    /**
     * Gets query for [[TeacherParticipantBranches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipantBranches()
    {
        return $this->hasMany(TeacherParticipantBranch::className(), ['teacher_participant_id' => 'id']);
    }
}
