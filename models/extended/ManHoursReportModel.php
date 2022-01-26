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

class ManHoursReportModel extends \yii\base\Model
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
        //$debug = '<table class="table table-bordered">';
        //$debug .= '<tr><td>Группа</td><td>Кол-во занятий выбранного педагога</td><td>Кол-во занятий всех педагогов</td><td>Кол-во учеников</td><td>Кол-во ч/ч</td></tr>';
        $debug = "Группа;Кол-во занятий выбранного педагога;Кол-во занятий всех педагогов;Кол-во учеников;Кол-во ч/ч\r\n";

        $debug2 = "ФИО обучающегося;Группа;Дата начала занятий;Дата окончания занятий;Отдел;Пол;Дата рождения;Раздел\r\n";

        $header = "Отчет по <br>";

        $f = 0; //для генерации заголовка

        $result = '<table class="table table-bordered">';
        foreach ($this->type as $oneType)
        {
            if ($oneType === '0')
            {
                $header .= 'человеко-часам<br> ';

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
                    $header .= '(';

                    $teachers = TeacherGroupWork::find()->where(['teacher_id' => $this->teacher])->all();
                    $tId = [];
                    $lessons = $lessons->all();
                    foreach ($teachers as $teacher)
                    {
                        $header .= $teacher->teacherWork->shortName.' ';
                        $tId[] = $teacher->training_group_id;
                    }
                    $header .= ') с '.$this->start_date.' по '.$this->end_date.'<br>';
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
                        $debug .= $tg->number.";";
                        $dLessons = LessonThemeWork::find()->joinWith('trainingGroupLesson trainingGroupLesson')
                            ->where(['teacher_id' => $dTeacherId])->andWhere(['IN', 'training_group_lesson_id', $tId])
                            ->andWhere(['trainingGroupLesson.training_group_id' => $tg->id])->all();
                        $debug .= count($dLessons).";";
                        $debug .= count(TrainingGroupLessonWork::find()->where(['training_group_id' => $tg->id])->andWhere(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date])->all()).";";
                        $debug .= count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $tg->id])->all()).";";
                        $statusArr = [];
                        if ($this->method == 0) $statusArr = [0, 2];
                        else $statusArr = [0, 1, 2, 3];
                        $dlessonsId = [];
                        $dlessons = $lessons;
                        $dlessons = $dlessons->all();
                        foreach ($dlessons as $dlesson) $dlessonsId[] = $this->teacher = $dlesson->training_group_lesson_id;
                        $debug .= count(VisitWork::find()->joinWith('trainingGroupLesson trainingGroupLesson')
                                ->where(['IN', 'training_group_lesson_id', $dlessonsId])->andWhere(['IN', 'status', $statusArr])
                                ->andWhere(['trainingGroupLesson.training_group_id' => $tg->id])->all());
                        $debug .= "\r\n";
                    }
                    //----------------
                }
                else
                {
                    $header .= 'с '.$this->start_date.' по '.$this->end_date.'<br>';
                    //ОТЛАДОЧНЫЙ ВЫВОД
                    $dGroups = TrainingGroupLessonWork::find()->joinWith(['trainingGroup trainingGroup'])->select('training_group_id')->distinct()
                        ->where(['>', 'lesson_date', $this->start_date])->andWhere(['<', 'lesson_date', $this->end_date])
                        ->andWhere(['IN', 'trainingGroup.branch_id', $this->branch])
                        ->andWhere(['IN', 'trainingGroup.training_program_id', $progsId])
                        ->andWhere(['IN', 'trainingGroup.budget', $this->budget])
                        ->all();
                    $dgIds = [];
                    foreach ($dGroups as $dGroup) $dgIds[] = $dGroup->training_group_id;
                    $dGroups = TrainingGroupWork::find()->where(['IN', 'id', $dgIds])->all();
                    foreach ($dGroups as $dGroup)
                    {
                        $debug .= $dGroup->number.";";
                        $newGroupsLessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $dGroup->id])->andWhere(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date])->all();
                        $nglIds = [];
                        foreach ($newGroupsLessons as $lesson) $nglIds[] = $lesson->id;
                        $debug .= count(LessonThemeWork::find()->where(['IN', 'id', $nglIds])->all()).";";
                        $debug .= count($newGroupsLessons).";";
                        $debug .= count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $dGroup->id])->all()).";";
                        $statusArr = [];
                        if ($this->method == 0) $statusArr = [0, 2];
                        else $statusArr = [0, 1, 2, 3];
                        $debug .= count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $nglIds])->andWhere(['IN', 'status', $statusArr])->all()).";";
                        $debug .= "\r\n";
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
            else
            {
                if ($f == 0)
                {
                    $header .= 'обучающимся<br> с ' . $this->start_date . ' по ' . $this->end_date;
                    $f = 1;
                }
            }
            if ($oneType === '1')
            {

                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2, 3];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['<', 'start_date', $this->start_date])->andWhere(['<', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];

                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td><b>1</b></td><td>Количество обучающихся, начавших обучение до '.$this->start_date.' завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';

                //ОТЛАДОЧНЫЙ ВЫВОД
                foreach ($parts as $part)
                {

                    $part = TrainingGroupParticipantWork::find()->where(['participant_id' => $part->participant_id])->andWhere(['IN', 'training_group_id', $groupsId])->one();
                    $debug2 .= $part->participantWork->fullName.";".$part->trainingGroupWork->number.";".$part->trainingGroupWork->start_date.";".$part->trainingGroupWork->finish_date.
                         ";".$part->trainingGroupWork->pureBranch.";".$part->participantWork->sex.";".$part->participantWork->birthdate.";1\r\n";
                }
                $debug2 .= "\r\n";
                //----------------
            }
            if ($oneType == '2')
            {
                
                if ($this->method == 0) $statusArr = [0, 2];
                else $statusArr = [0, 1, 2];

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['>', 'start_date', $this->start_date])->andWhere(['<', 'start_date', $this->end_date])->andWhere(['>', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td><b>2</b></td><td>Количество обучающихся, начавших обучение в период с '.$this->start_date.' по '.$this->end_date.' и завершивших обучение после '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';

                //ОТЛАДОЧНЫЙ ВЫВОД
                foreach ($parts as $part)
                {

                    $part = TrainingGroupParticipantWork::find()->where(['participant_id' => $part->participant_id])->andWhere(['IN', 'training_group_id', $groupsId])->one();
                    $debug2 .= $part->participantWork->fullName.";".$part->trainingGroupWork->number.";".$part->trainingGroupWork->start_date.";".$part->trainingGroupWork->finish_date.
                        ";".$part->trainingGroupWork->pureBranch.";".$part->participantWork->sex.";".$part->participantWork->birthdate.";2\r\n";
                }
                $debug2 .= "\r\n";
                //----------------
            }
            if ($oneType == '3')
            {

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['>', 'start_date', $this->start_date])->andWhere(['<', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];

                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td><b>3</b></td><td>Количество обучающихся, начавших обучение после '.$this->start_date.' и завершивших до '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';

                //ОТЛАДОЧНЫЙ ВЫВОД
                foreach ($parts as $part)
                {

                    $part = TrainingGroupParticipantWork::find()->where(['participant_id' => $part->participant_id])->andWhere(['IN', 'training_group_id', $groupsId])->one();
                    $debug2 .= $part->participantWork->fullName.";".$part->trainingGroupWork->number.";".$part->trainingGroupWork->start_date.";".$part->trainingGroupWork->finish_date.
                        ";".$part->trainingGroupWork->pureBranch.";".$part->participantWork->sex.";".$part->participantWork->birthdate.";3\r\n";
                }
                $debug2 .= "\r\n";
                //----------------
            }
            if ($oneType == '4')
            {

                $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
                    ->where(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                        ->where(['<', 'start_date', $this->start_date])->andWhere(['>', 'finish_date', $this->end_date])])
                    ->andWhere(['IN', 'branch_id', $this->branch])
                    ->andWhere(['IN', 'trainingProgram.focus_id', $this->focus])
                    ->andWhere(['IN', 'budget', $this->budget])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                if ($this->unic == 1)
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->all();
                else
                    $parts = TrainingGroupParticipantWork::find()->select('participant_id')->where(['IN', 'training_group_id', $groupsId])->all();

                $result .= '<tr><td><b>4</b></td><td>Количество обучающихся, начавших обучение до '.$this->start_date.' и завершивших после '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';

                //ОТЛАДОЧНЫЙ ВЫВОД
                foreach ($parts as $part)
                {

                    $part = TrainingGroupParticipantWork::find()->where(['participant_id' => $part->participant_id])->andWhere(['IN', 'training_group_id', $groupsId])->one();
                    $debug2 .= $part->participantWork->fullName.";".$part->trainingGroupWork->number.";".$part->trainingGroupWork->start_date.";".$part->trainingGroupWork->finish_date.
                        ";".$part->trainingGroupWork->pureBranch.";".$part->participantWork->sex.";".$part->participantWork->birthdate.";4\r\n";
                }
                $debug2 .= "\r\n";
                //----------------
            }
        }
        $result = $result.'</table>';

        return [$result, $debug, $debug2, $header];
    }

    public function save()
    {
        return true;
    }
}