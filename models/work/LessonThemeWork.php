<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "lesson_theme".
 *
 * @property int $id
 * @property int $training_group_lesson_id
 * @property int $teacher_id
 * @property string $theme
 *
 * @property TrainingGroupLesson $trainingGroupLesson
 */
class LessonTheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_theme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_lesson_id', 'theme', 'teacher_id'], 'required'],
            [['training_group_lesson_id', 'teacher_id'], 'integer'],
            [['theme'], 'string', 'max' => 1000],
            [['training_group_lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupLesson::className(), 'targetAttribute' => ['training_group_lesson_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'teacher_id' => 'Teacher ID',
            'theme' => 'Theme',
        ];
    }

    /**
     * Gets query for [[TrainingGroupLesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLesson()
    {
        return $this->hasOne(TrainingGroupLesson::className(), ['id' => 'training_group_lesson_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher_id']);
    }
}