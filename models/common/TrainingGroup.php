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
 * @property string $start_date
 * @property string $finish_date
 * @property string|null $photos
 * @property string|null $present_data
 * @property string|null $work_data
 * @property int $open
 * @property int|null $schedule_type
 * @property int $budget
 * @property int|null $branch_id
 * @property int $order_status
 *
 * @property GroupErrors[] $groupErrors
 * @property OrderGroup[] $orderGroups
 * @property TeacherGroup[] $teacherGroups
 * @property People $teacher
 * @property TrainingProgram $trainingProgram
 * @property Branch $branch
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
            [['number', 'start_date', 'finish_date'], 'required'],
            [['training_program_id', 'teacher_id', 'open', 'schedule_type', 'budget', 'branch_id', 'order_status'], 'integer'],
            [['start_date', 'finish_date', 'schedule_type'], 'safe'],
            [['number'], 'string', 'max' => 100],
            [['photos', 'present_data', 'work_data'], 'string', 'max' => 1000],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::className(), 'targetAttribute' => ['training_program_id' => 'id']],
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
            'number' => 'Номер',
            'training_program_id' => 'Образовательная программа',
            'programName' => 'Образовательная программа',
            'teacher_id' => 'Педагог',
            'teacherName' => 'Педагог',
            'start_date' => 'Дата начала занятий',
            'finish_date' => 'Дата окончания занятий',
            'photos' => 'Фотоматериалы',
            'photosFile' => 'Фотоматериалы',
            'present_data' => 'Презентационные материалы',
            'presentDataFile' => 'Презентационные материалы',
            'work_data' => 'Рабочие материалы',
            'workDataFile' => 'Рабочие материалы',
            'open' => 'Утвердить расписание',
            'openText' => 'Расписание утверждено',
            'participantNames' => 'Состав',
            'lessonDates' => 'Расписание',
            'scheduleType' => 'Тип расписания',
            'ordersName' => 'Приказы',
            'budget' => 'Бюджет',
            'fileParticipants' => 'Загрузить учащихся из файла',
            'teachersList' => 'Педагог(-и)',
            'branch_id' => 'Отдел производящий учёт',
            'order_status' => 'Статус добавления приказов',
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
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
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
