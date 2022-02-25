<?php

namespace app\models\common;

use Yii;
use yii\behaviors\TimestampBehavior;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;

/**
 * This is the model class for table "teacher_participant".
 *
 * @property int $id
 * @property int $participant_id
 * @property int $teacher_id
 * @property int $foreign_event_id
 * @property int|null $branch_id
 * @property string $focus
 * @property int $teacher2_id
 * @property int $responsible2_id
 *
 * @property ForeignEvent $foreignEvent
 * @property ForeignEventParticipants $participant
 * @property People $teacher
 * @property Branch $branch
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
            [['participant_id', 'teacher_id', 'foreign_event_id', 'branch_id', 'teacher2_id', 'responsible2_id'], 'integer'],
            [['focus'], 'string', 'max' => 1000],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class' => SaveRelationsBehavior::className(),
                'relations' => [
                    'teacherParticipantBranches',
                ],
            ],
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
            'foreign_event_id' => 'Foreign Event ID',
            'branch_id' => 'Branch ID',
            'focus' => 'Focus',
            'teacher2_id' => 'Teacher2 ID',
            'responsible2_id' => 'Responsible2 ID',
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
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
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
