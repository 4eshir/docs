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
 * @property int|null $branch_id
 * @property int|null $focus
 * @property int|null $allow_remote_id
 * @property string|null $nomination
 *
 * @property ParticipantAchievement[] $participantAchievements
 * @property ParticipantFiles[] $participantFiles
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property People $teacher
 * @property Branch $branch
 * @property People $teacher2
 * @property Focus $focus0
 * @property AllowRemote $allowRemote
 * @property TeacherParticipantBranch[] $teacherParticipantBranches
 * @property Team[] $teams
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
            [['participant_id', 'teacher_id', 'foreign_event_id'], 'required'],
            [['participant_id', 'teacher_id', 'teacher2_id', 'foreign_event_id', 'branch_id', 'focus', 'allow_remote_id'], 'integer'],
            [['nomination'], 'string', 'max' => 1000],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['teacher2_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher2_id' => 'id']],
            [['focus'], 'exist', 'skipOnError' => true, 'targetClass' => Focus::className(), 'targetAttribute' => ['focus' => 'id']],
            [['allow_remote_id'], 'exist', 'skipOnError' => true, 'targetClass' => AllowRemote::className(), 'targetAttribute' => ['allow_remote_id' => 'id']],
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
            'teacher2_id' => 'Teacher 2 ID',
            'foreign_event_id' => 'Foreign Event ID',
            'branch_id' => 'Branch ID',
            'focus' => 'Focus',
            'allow_remote_id' => 'Allow Remote ID',
            'nomination' => 'Nomination',
        ];
    }

    /**
     * Gets query for [[ParticipantAchievements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantAchievements()
    {
        return $this->hasMany(ParticipantAchievement::className(), ['teacher_participant_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipantFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantFiles()
    {
        return $this->hasMany(ParticipantFiles::className(), ['teacher_participant_id' => 'id']);
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
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
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
     * Gets query for [[Focus0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFocus0()
    {
        return $this->hasOne(Focus::className(), ['id' => 'focus']);
    }

    /**
     * Gets query for [[AllowRemote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAllowRemote()
    {
        return $this->hasOne(AllowRemote::className(), ['id' => 'allow_remote_id']);
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

    /**
     * Gets query for [[Teams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['teacher_participant_id' => 'id']);
    }
}
