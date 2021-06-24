<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "training_group_lesson".
 *
 * @property int $id
 * @property string $lesson_date
 * @property string $lesson_start_time
 * @property string $lesson_end_time
 * @property int $duration
 * @property int $branch_id
 * @property int $auditorium_id
 * @property int $training_group_id
 *
 * @property TrainingGroup $trainingGroup
 */
class TrainingGroupLesson extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group_lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_date', 'lesson_start_time', 'lesson_end_time'], 'string'],
            [['duration', 'auditorium_id', 'training_group_id', 'branch_id'], 'integer'],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_date' => 'Lesson Date',
            'lesson_start_time' => 'Lesson Start Time',
            'lesson_end_time' => 'Lesson End Time',
            'duration' => 'Duration',
            'branch_id' => 'Branch ID',
            'auditorium_id' => 'Auditorium ID',
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
        return $this->hasOne(TrainingGroup::className(), ['id' => 'training_group_id']);
    }
}
