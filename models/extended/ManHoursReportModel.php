<?php


namespace app\models\extended;


use app\models\work\LessonThemeWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\VisitWork;

class ManHoursReportModel extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $type;
    /*
     * 0 - человеко-часы
     * 1 - всего уникальных людей
     * 2 - всего людей
     */
    public $branch;
    public $budget;
    public $teacher;
    public $focus;
    public $method;


    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['type', 'branch', 'budget', 'focus'], 'safe'],
            [['method', 'teacher'], 'integer']
        ];
    }

    public function generateReport()
    {
        $result = '<table class="table table-bordered">';
        foreach ($this->type as $oneType)
        {
            if ($oneType === '0')
            {
                $lessons = TrainingGroupLessonWork::find()->joinWith(['trainingGroup trainingGroup'])
                    ->where(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date]); //все занятия, попадающие
                                                                                                                        //попадающие в промежуток
                $lessons = $lessons->andWhere(['IN', 'trainingGroup.branch_id', $this->branch]);

                $progs = TrainingProgramWork::find()->where(['IN', 'focus_id', $this->focus])->all();
                $progsId = [];
                foreach ($progs as $prog) $progsId[] = $prog->id;

                $lessons = $lessons->andWhere(['IN', 'trainingGroup.training_program_id', $progsId]);
                $lessons = $lessons->andWhere(['IN', 'trainingGroup.budget', $this->budget]);

                if ($this->teacher !== "")
                {
                    $teachers = TeacherGroupWork::find()->where(['teacher_id' => $this->teacher])->all();
                    $tId = [];
                    foreach ($teachers as $teacher) $tId[] = $teacher->training_group_id;
                    $lessons = TrainingGroupLessonWork::find()->where(['IN', 'training_group_id', $tId])->all();
                    $tId = [];
                    foreach ($lessons as $lesson) $tId[] = $lesson->id;
                    $lessons = LessonThemeWork::find()->where(['teacher_id' => $this->teacher])->andWhere(['IN', 'training_group_lesson_id', $tId]);
                    var_dump(count($lessons->all()));
                    var_dump($this->start_date);
                    var_dump($this->end_date);
                }

                $lessons = $lessons->all();

                $lessonsId = [];
                foreach ($lessons as $lesson) $lessonsId[] = $this->teacher !== "" ? $lesson->training_group_lesson_id : $lesson->id;
                $statusArr = [];
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2];
                $visit = VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['IN', 'status', $statusArr])->all();
                //var_dump($visit->createCommand()->getRawSql());
                $result .= '<tr><td>Количество человеко-часов за период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($visit).' ч/ч'.'</td></tr>';
            }
            if ($oneType === '1')
            {
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество уникальных обучающися, завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
            if ($oneType === '2')
            {
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество уникальных обучающися, завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
            if ($oneType == '3')
            {
                
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['<=', 'start_date', $this->start_date])->andWhere(['>=', 'finish_date', $this->end_date])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество обучающихся, проходящих обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
            if ($oneType == '4')
            {
                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['<=', 'start_date', $this->start_date])->andWhere(['>=', 'finish_date', $this->end_date])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts1 = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts2 = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество всех обучающися в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.(count($parts1) + count($parts2)). ' чел.'.'</td></tr>';

            }
        }
        $result = $result.'</table>';
        return $result;
    }

    public function save()
    {
        return true;
    }
}