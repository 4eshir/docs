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
    public $auds;
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
            ['auds', 'safe'],
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

    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
    }

    public function getFullName()
    {
        $aud = $this->auditorium_id;
        if ($aud === null) return '--- ('.$this->branch->name.')';
        else return $this->auditorium->name.' ('.$this->branch->name.')';
    }

    public function checkValideTime($group_id)
    {
        $lessons = TrainingGroupLesson::find()->where(['!=', 'id', $this->id])->all();
        $result = [];
        foreach ($lessons as $lesson)
        {
            if (($this->lesson_start_time < $lesson->lesson_end_time && $this->lesson_end_time > $lesson->lesson_start_time) && $lesson->lesson_date == $this->lesson_date && $lesson->auditorium_id == $this->auditorium_id)
                $result[] = $lesson->id;
        }
        return $result;
    }

    public function beforeSave($insert)
    {
        $str = '+'.$this->trainingGroup->trainingProgram->hour_capacity.' minutes';
        $this->lesson_end_time = date("H:i", strtotime($str, strtotime($this->lesson_start_time)));
        $this->duration = 1;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}