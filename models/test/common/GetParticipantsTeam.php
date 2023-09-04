<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_participants_team".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $teacher_participant_id
 *
 * @property GetParticipantsTeacherParticipant $teacherParticipant
 */
class GetParticipantsTeam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_participants_team';
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
            [['teacher_participant_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
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
            'name' => 'Name',
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
