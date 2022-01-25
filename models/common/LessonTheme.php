<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "lesson_theme".
 *
 * @property int $id
 * @property string $theme
 * @property int $training_group_lesson_id
 * @property int $teacher_id
 * @property int|null $control_type_id
 *
 * @property TrainingGroupLesson $trainingGroupLesson
 * @property People $teacher
 * @property ControlType $controlType
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
            [['theme', 'training_group_lesson_id', 'teacher_id'], 'required'],
            [['training_group_lesson_id', 'teacher_id', 'control_type_id'], 'integer'],
            [['theme'], 'string', 'max' => 1000],
            [['training_group_lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupLesson::className(), 'targetAttribute' => ['training_group_lesson_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['control_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ControlType::className(), 'targetAttribute' => ['control_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme' => 'Theme',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'teacher_id' => 'Teacher ID',
            'control_type_id' => 'Control Type ID',
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
     * Gets query for [[ControlType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getControlType()
    {
        return $this->hasOne(ControlType::className(), ['id' => 'control_type_id']);
    }
}
