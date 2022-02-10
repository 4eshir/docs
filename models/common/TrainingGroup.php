<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "training_group".
 *
 * @property int $id
 * @property string $number
 * @property int|null $training_program_id
 * @property int|null $teacher_id
 * @property string|null $start_date
 * @property string|null $finish_date
 * @property string|null $photos
 * @property string|null $present_data
 * @property string|null $work_data
 * @property int $open
 * @property int|null $schedule_type
 * @property int $budget
 * @property int $branch_id
 * @property int $order_status
 * @property int $order_stop
 * @property int $archive
 *
 * @property GroupErrors[] $groupErrors
 * @property OrderGroup[] $orderGroups
 * @property TeacherGroup[] $teacherGroups
 * @property People $teacher
 * @property TrainingProgram $trainingProgram
 * @property TrainingGroupLesson[] $trainingGroupLessons
 * @property TrainingGroupParticipant[] $trainingGroupParticipants
 */
class TrainingGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'branch_id', 'order_status'], 'required'],
            [['training_program_id', 'teacher_id', 'open', 'schedule_type', 'budget', 'branch_id', 'order_status', 'order_stop', 'archive'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['number'], 'string', 'max' => 100],
            [['photos', 'present_data', 'work_data'], 'string', 'max' => 1000],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::className(), 'targetAttribute' => ['training_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'training_program_id' => 'Training Program ID',
            'teacher_id' => 'Teacher ID',
            'start_date' => 'Start Date',
            'finish_date' => 'Finish Date',
            'photos' => 'Photos',
            'present_data' => 'Present Data',
            'work_data' => 'Work Data',
            'open' => 'Open',
            'schedule_type' => 'Schedule Type',
            'budget' => 'Budget',
            'branch_id' => 'Branch ID',
            'order_status' => 'Order Status',
            'order_stop' => 'Order Stop',
            'archive' => 'Archive',
        ];
    }

    /**
     * Gets query for [[GroupErrors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupErrors()
    {
        return $this->hasMany(GroupErrors::className(), ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[OrderGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGroups()
    {
        return $this->hasMany(OrderGroup::className(), ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TeacherGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherGroups()
    {
        return $this->hasMany(TeacherGroup::className(), ['training_group_id' => 'id']);
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
     * Gets query for [[TrainingProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgram()
    {
        return $this->hasOne(TrainingProgram::className(), ['id' => 'training_program_id']);
    }

    /**
     * Gets query for [[TrainingGroupLessons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLessons()
    {
        return $this->hasMany(TrainingGroupLesson::className(), ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::className(), ['training_group_id' => 'id']);
    }
}
