<?php

namespace app\models\common;

use app\models\components\ExcelWizard;
use app\models\components\FileWizard;
use Mpdf\Tag\Tr;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "training_group".
 *
 * @property int $id
 * @property string $number
 * @property int|null $training_program_id
 * @property int $teacher_id
 * @property string $start_date
 * @property string $finish_date
 * @property string|null $photos
 * @property string|null $present_data
 * @property string|null $work_data
 * @property int $open
 * @property int $schedule_type
 * @property int $budget
 *
 * @property People $teacher
 * @property TrainingProgram $trainingProgram
 * @property TrainingProgramParticipant[] $trainingProgramParticipants
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
            [['number', 'teacher_id', 'start_date', 'finish_date', 'budget'], 'required'],
            [['training_program_id', 'teacher_id', 'open', 'budget'], 'integer'],
            [['start_date', 'finish_date', 'schedule_type'], 'safe'],
            [['photos', 'present_data', 'work_data', 'number'], 'string', 'max' => 1000],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
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
        ];
    }

    /**
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher_Id']);
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
     * Gets query for [[TrainingProgramParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgramParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::className(), ['training_group_id' => 'id']);
    }


}
