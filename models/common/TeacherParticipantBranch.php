<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "teacher_participant_branch".
 *
 * @property int $id
 * @property int $teacher_participant_id
 * @property int $branch_id
 *
 * @property Branch $branch
 * @property TeacherParticipant $teacherParticipant
 */
class TeacherParticipantBranch extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_participant_branch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id'], 'required'],
            [['teacher_participant_id', 'branch_id'], 'integer'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
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
            'branch_id' => 'Branch ID',
        ];
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
     * Gets query for [[TeacherParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipant()
    {
        return $this->hasOne(TeacherParticipant::className(), ['id' => 'teacher_participant_id']);
    }
}
