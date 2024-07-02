<?php

namespace app\models\common;

use app\models\work\TrainingGroupExpertWork;
use Yii;

/**
 * This is the model class for table "training_group".
 *
 * @property int $id
 * @property string $number
 * @property int|null $training_program_id
 * @property int|null $teacher_id
 * @property string $start_date
 * @property string $finish_date
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
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property string|null $protection_date
 * @property int|null $protection_confirm
 * @property int|null $is_network
 * @property string|null $photos_link
 * @property string|null $present_data_link
 * @property string|null $work_data_link
 *
 * @property Branch $branch
 * @property User $creator
 * @property User $lastEdit
 * @property People $teacher
 * @property TrainingProgram $trainingProgram
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
            [['id', 'number', 'start_date', 'finish_date', 'branch_id', 'order_status'], 'required'],
            [['id', 'training_program_id', 'teacher_id', 'open', 'schedule_type', 'budget', 'branch_id', 'order_status', 'order_stop', 'archive', 'creator_id', 'last_edit_id', 'protection_confirm', 'is_network'], 'integer'],
            [['start_date', 'finish_date', 'protection_date'], 'safe'],
            [['number'], 'string', 'max' => 100],
            [['photos', 'present_data', 'work_data'], 'string', 'max' => 1000],
            [['photos_link', 'present_data_link', 'work_data_link'], 'string', 'max' => 256],
            [['id'], 'unique'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'protection_date' => 'Protection Date',
            'protection_confirm' => 'Protection Confirm',
            'is_network' => 'Is Network',
            'photos_link' => 'Photos Link',
            'present_data_link' => 'Present Data Link',
            'work_data_link' => 'Work Data Link',
        ];
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
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[LastEdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEdit()
    {
        return $this->hasOne(User::className(), ['id' => 'last_edit_id']);
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
     * Gets query for [[TrainingGroupExperts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupExperts()
    {
        return $this->hasMany(TrainingGroupExpertWork::className(), ['training_group_id' => 'id']);
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
