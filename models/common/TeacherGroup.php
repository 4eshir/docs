<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "teacher_group".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $training_group_id
 *
 * @property People $teacher
 * @property TrainingGroup $trainingGroup
 */
class TeacherGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_id'], 'required'],
            [['teacher_id', 'training_group_id'], 'integer'],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
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
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::className(), ['id' => 'training_group_id']);
    }
}
