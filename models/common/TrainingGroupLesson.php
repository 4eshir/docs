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
 * @property int|null $auditorium_id
 * @property int $training_group_id
 * @property int|null $branch_id
 *
 * @property LessonTheme[] $lessonThemes
 * @property TrainingGroup $trainingGroup
 * @property Auditorium $auditorium
 * @property Branch $branch
 * @property Visit[] $visits
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
            [['lesson_date', 'lesson_start_time', 'lesson_end_time', 'duration', 'training_group_id'], 'required'],
            [['lesson_date', 'lesson_start_time', 'lesson_end_time'], 'safe'],
            [['duration', 'auditorium_id', 'training_group_id', 'branch_id'], 'integer'],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
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
            'auditorium_id' => 'Auditorium ID',
            'training_group_id' => 'Training Group ID',
            'branch_id' => 'Branch ID',
        ];
    }

    /**
     * Gets query for [[LessonThemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonThemes()
    {
        return $this->hasMany(LessonTheme::className(), ['training_group_lesson_id' => 'id']);
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

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
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
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::className(), ['training_group_lesson_id' => 'id']);
    }
}
