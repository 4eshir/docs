<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_lesson_theme".
 *
 * @property int $id
 * @property int|null $training_group_lesson_id
 * @property int|null $teacher_id
 *
 * @property GetGroupParticipantsTrainingGroupLesson $trainingGroupLesson
 */
class GetGroupParticipantsLessonTheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_lesson_theme';
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
            [['training_group_lesson_id', 'teacher_id'], 'integer'],
            [['training_group_lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingGroupLesson::className(), 'targetAttribute' => ['training_group_lesson_id' => 'id']],
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
        ];
    }

    /**
     * Gets query for [[TrainingGroupLesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLesson()
    {
        return $this->hasOne(GetGroupParticipantsTrainingGroupLesson::className(), ['id' => 'training_group_lesson_id']);
    }
}
