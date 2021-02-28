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
 * @property string $class
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
            [['lesson_date', 'lesson_start_time', 'lesson_end_time', 'duration', 'class'], 'required'],
            [['lesson_date', 'lesson_start_time', 'lesson_end_time'], 'safe'],
            [['duration'], 'integer'],
            [['class'], 'string', 'max' => 1000],
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
            'class' => 'Class',
        ];
    }
}
