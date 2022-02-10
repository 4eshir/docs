<?php


namespace app\models\extended;


use app\models\common\TrainingGroup;
use app\models\work\LessonThemeWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\VisitWork;
use Mpdf\Tag\P;
use yii\db\Query;

class UsefulSideReportModel extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $type;
    public $unic;
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
            [['method', 'teacher', 'unic'], 'integer']
        ];
    }

    public function generateReport()
    {
        $debug = '<table class="table table-bordered">';
        $debug .= '<tr><td>Группа</td><td>Кол-во занятий выбранного педагога</td><td>Кол-во занятий всех педагогов</td><td>Кол-во учеников</td><td>Кол-во ч/ч</td></tr>';
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
                    $lessons = $lessons->all();
                    foreach ($teachers as $teacher) $tId[] = $teacher->training_group_id;
                    $tIdCopy = $tId;
                    $lessons = TrainingGroupLessonWork::find()->joinWith('trainingGroup trainingGroup')->where(['IN', 'training_group_id', $tIdCopy])
                        ->andWhere(['>=', 'lesson_date', $this->start_date])
                        ->andWhere(['<=', 'lesson_date', $this->end_date])
                        ->andWhere(['IN', 'trainingGroup.branch_id', $this->branch])
                        ->andWhere(['IN', 'trainingGroup.training_program_id', $progsId])
                        ->andWhere(['IN', 'trainingGroup.budget', $this->budget])
                        ->all();
                    $tId = [];
                    foreach ($lessons as $lesson) $tId[] = $lesson->id;
                    $lessons = LessonThemeWork::find()->where(['teacher_id' => $this->teacher])->andWhere(['IN', 'training_group_lesson_id', $tId]);

                    //ОТЛАДОЧНЫЙ ВЫВОД
                    $newLessons = TrainingGroupLessonWork::find()->joinWith('trainingGroup trainingGroup')->where(['IN', 'training_group_id', $tIdCopy])
                        ->andWhere(['>=', 'lesson_date', $this->start_date])
                        ->andWhere(['<=', 'lesson_date', $this->end_date])
                        ->andWhere(['IN', 'trainingGroup.branch_id', $this->branch])
                        ->andWhere(['IN', 'trainingGroup.training_program_id', $progsId])
                        ->andWhere(['IN', 'trainingGroup.budget', $this->budget])
                        ->all();
                    $nlIds = [];
                    foreach ($newLessons as $lesson) $nlIds[] = $lesson->training_group_id;
                    $tgs = TrainingGroupWork::find()->where(['IN', 'id', $nlIds])->all();
                    //----------------

                    //ОТЛАДОЧНЫЙ ВЫВОД
                    $dTeacherId = $this->teacher;
                    foreach ($tgs as $tg)
                    {
                        $debug .= '<tr><td>'.$tg->number.'</td>';
                        $dLessons = LessonThemeWork::find()->joinWith('trainingGroupLesson trainingGroupLesson')
                            ->where(['teacher_id' => $dTeacherId])->andWhere(['IN', 'training_group_lesson_id', $tId])
                            ->andWhere(['trainingGroupLesson.training_group_id' => $tg->id])->all();
                        $debug .= '<td>'.count($dLessons).'</td>';
                        $debug .= '<td>'.count(TrainingGroupLessonWork::find()->where(['training_group_id' => $tg->id])->andWhere(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date])->all()).'</td>';
                        $debug .= '<td>'.count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $tg->id])->all()).'</td>';
                        $statusArr = [];
                        if ($this->method == 0) $statusArr = [0, 2];
                        else $statusArr = [0, 1, 2, 3];
                        $dlessonsId = [];
                        $dlessons = $lessons;
                        $dlessons = $dlessons->all();
                        foreach ($dlessons as $dlesson) $dlessonsId[] = $this->teacher = $dlesson->training_group_lesson_id;
                        $debug .= '<td>'.count(VisitWork::find()->joinWith('trainingGroupLesson trainingGroupLesson')
                                ->where(['IN', 'training_group_lesson_id', $dlessonsId])->andWhere(['IN', 'status', $statusArr])
                                ->andWhere(['trainingGroupLesson.training_group_id' => $tg->id])->all()).'</td>';
                        $debug .= '</tr>';
                    }
                    //----------------
                }
                else
                {
                    //ОТЛАДОЧНЫЙ ВЫВОД
                    $dGroups = TrainingGroupLessonWork::find()->joinWith(['trainingGroup trainingGroup'])->select('training_group_id')->distinct()
                        ->where(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date])
                        ->andWhere(['IN', 'trainingGroup.branch_id', $this->branch])
                        ->andWhere(['IN', 'trainingGroup.training_program_id', $progsId])
                        ->andWhere(['IN', 'trainingGroup.budget', $this->budget])
                        ->all();
                    $dgIds = [];
                    foreach ($dGroups as $dGroup) $dgIds[] = $dGroup->training_group_id;
                    $dGroups = TrainingGroupWork::find()->where(['IN', 'id', $dgIds])->all();
                    foreach ($dGroups as $dGroup)
                    {
                        $debug .= '<tr><td>'.$dGroup->number.'</td>';
                        $newGroupsLessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $dGroup->id])->andWhere(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date])->all();
                        $nglIds = [];
                        foreach ($newGroupsLessons as $lesson) $nglIds[] = $lesson->id;
                        $debug .= '<td>'.count(LessonThemeWork::find()->where(['IN', 'id', $nglIds])->all()).'</td>';
                        $debug .= '<td>'.count($newGroupsLessons).'</td>';
                        $debug .= '<td>'.count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $dGroup->id])->all()).'</td>';
                        $statusArr = [];
                        if ($this->method == 0) $statusArr = [0, 2];
                        else $statusArr = [0, 1, 2, 3];
                        $debug .= '<td>'.count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $nglIds])->andWhere(['IN', 'status', $statusArr])->all()).'</td>';
                        $debug .= '</tr>';
                    }
                    //----------------
                }

                $lessons = $lessons->all();

                $lessonsId = [];
                foreach ($lessons as $lesson) $lessonsId[] = $this->teacher !== "" ? $lesson->training_group_lesson_id : $lesson->id;
                $statusArr = [];
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2, 3];
                $visit = VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['IN', 'status', $statusArr])->all();
                $result .= '<tr><td>Количество человеко-часов за период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($visit).' ч/ч'.'</td></tr>';


            }
            if ($oneType === '1')
            {
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2, 3];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество обучающихся, начавших обучение до '.$this->start_date.' завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
            if ($oneType == '2')
            {
                
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['>=', 'start_date', $this->start_date])->andWhere(['<=', 'start_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество обучающихся, начавших обучение в период с '.$this->start_date.' по '.$this->end_date.' и завершивших обучение после '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
            if ($oneType == '3')
            {
                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['>=', 'start_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество обучающихся, начавших обучение после '.$this->start_date.' и завершивших до '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';

            }
            if ($oneType == '4')
            {
                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['<=', 'start_date', $this->start_date])->andWhere(['>=', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td>Количество обучающихся, начавших обучение до '.$this->start_date.' и завершивших после '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';

            }
        }
        $result = $result.'</table>';
        $debug = $debug.'</table>';

        return [$result, $debug];
    }

    public function save()
    {
        return true;
    }
}