<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_teacher_group".
 *
 * @property int $id
 * @property int|null $teacher_id
 * @property int|null $training_group_id
 *
 * @property GetGroupParticipantsTrainingGroup $trainingGroup
 */
class GetGroupParticipantsTeacherGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_teacher_group';
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
            [['teacher_id', 'training_group_id'], 'integer'],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'training_group_id' => 'Training Group ID',
        ];
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(GetGroupParticipantsTrainingGroup::className(), ['id' => 'training_group_id']);
    }
}
