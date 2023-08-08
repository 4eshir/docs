<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_participants_teacher_participant_branch".
 *
 * @property int $id
 * @property int|null $branch_id
 * @property int|null $teacher_participant_id
 *
 * @property GetParticipantsTeacherParticipant $teacherParticipant
 */
class GetParticipantsTeacherParticipantBranch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_participants_teacher_participant_branch';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_report_test');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'teacher_participant_id'], 'integer'],
            [['teacher_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetParticipantsTeacherParticipant::className(), 'targetAttribute' => ['teacher_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'teacher_participant_id' => 'Teacher Participant ID',
        ];
    }

    /**
     * Gets query for [[TeacherParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipant()
    {
        return $this->hasOne(GetParticipantsTeacherParticipant::className(), ['id' => 'teacher_participant_id']);
    }
}
