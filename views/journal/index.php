<?php

use app\models\work\TrainingGroupWork;
use app\models\work\UserWork;
use app\models\components\UserRBAC;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\JournalModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Электронный журнал';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    select:focus {outline:none;}

    div.containerTable {
        overflow: scroll;
        max-width: 100%;
        max-height: 600px;
    }

    th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
    }

    tbody th {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
    }

    thead th:first-child {
        left: 0;
        z-index: 1;
    }

    thead th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
    }

    td, th {
        padding: 0.5em;
        vertical-align: middle;
        text-align: center;
    }

    thead th, tbody th {
        background: #FFF;
    }

</style>

<?php
//bfbf
    $parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
    $lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();
    $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
    $form = ActiveForm::begin(); ?>
    <?php
    $groups = \app\models\components\RoleBaseAccess::getGroupsByRole(Yii::$app->user->identity->getId());
    /*$groups = TrainingGroupWork::find()->where(['teacher_id' => $user->aka])->all();
    if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 22)) //доступ на просмотр ВСЕХ групп
    {
        $groups = TrainingGroupWork::find()->all();
    }
    else if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 24)) //доступ на просмотр групп СВОЕГО ОТДЕЛА
    {
        $branchs = \app\models\work\PeoplePositionBranchWork::find()->select('branch_id')->distinct()->where(['people_id' => $user->aka])->all();
        if ($branchs !== null)
        {
            $branchs_id = [];
            foreach ($branchs as $branch) $branchs_id[] = $branch->branch_id;
            $groups_id = \app\models\work\TrainingGroupLessonWork::find()->select('training_group_id')->distinct()->where(['in', 'branch_id', $branchs_id])->all();
            $newGroups_id = [];
            foreach ($groups_id as $group_id) $newGroups_id[] = $group_id->training_group_id;
            $groups = TrainingGroupWork::find()->where(['in', 'id', $newGroups_id])->all();
        }
    }
    else
    {
        $teachers = \app\models\work\TeacherGroupWork::find()->select('training_group_id')->distinct()->where(['teacher_id' => $user->aka])->all();
        $teachers_id = [];
        foreach ($teachers as $teacher) $teachers_id[] = $teacher->training_group_id;
        $groups = TrainingGroupWork::find()->where(['in', 'id', $teachers_id])->all();
    }
    if ($groups !== null)
        $items =  \yii\helpers\ArrayHelper::map($groups,'id','number');
    else
    {
        $tgroups = \app\models\work\TeacherGroupWork::find()->where(['teacher_id' => $user->aka])->all();
        $tgroups = \yii\helpers\ArrayHelper::map($tgroups, 'id', 'training_group_id');
        $groups = TrainingGroupWork::find()->where(['in', 'id', $tgroups])->all();
        $items = \yii\helpers\ArrayHelper::map($groups, 'id', 'number');
    }*/
    $items =  \yii\helpers\ArrayHelper::map($groups->all(),'id','number');
    $params = [
        'prompt' => '',
    ];
    echo '<div class="col-xs-3">';
    echo $form->field($model, 'trainingGroup')->dropDownList($items,$params)->label('Группа №');
    echo '</div>';
    ?>
    <div class="form-group col-xs-5" style="padding-top: 1.75em;">
        <?= Html::submitButton('Показать расписание', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Вернуться в карточку группы', \yii\helpers\Url::to(['training-group/view', 'id' => $model->trainingGroup]), ['class' => 'btn btn-warning']) ?>
    </div>
<?php ActiveForm::end(); ?>
<div  style="padding-top: 1.75em;">
    <?php
    echo Html::a("Переключиться в режим редактирования", \yii\helpers\Url::to(['journal/index-edit', 'group_id' => $model->trainingGroup]), ['class'=>'btn btn-success'])
    ?>
</div>

<?php
    echo '<br>';
    echo '<div class="containerTable" id="tableId">';
    echo '<table class="table table-bordered">';
    echo '<tr><td>ФИО ученика / Даты занятий</td>';
    foreach ($lessons as $lesson)
    {
        echo "<td>".date("d.m", strtotime($lesson->lesson_date))."</td>";
    }
    echo '</tr>';
    $counter = 0;
    foreach ($parts as $part)
    {
        $tr = '<tr>';
        if ($part->status == 1)
            $tr = '<tr style="background:#f08080">';
        //echo $tr.'<td>'.$part->participantWork->shortName.'</td>';
        echo $tr.'<td>'.Html::a($part->participantWork->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participantWork->id])).'</td>';
        foreach ($lessons as $lesson)
        {
            //$visits = \app\models\work\VisitWork::find()->where(['training_group_lesson_id' => $lesson->id])->andWhere(['foreign_event_participant_id' => $part->participant->id])->one();
            $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$counter]])->one();
            echo $visits->prettyStatus;
            $counter++;
        }
        echo '</tr>';
    }
    echo '</table></div><br><br>';
    echo '<h4>Тематический план занятий</h4><br>';
    echo '<div style="overflow-y: scroll; max-height: 400px; margin-bottom: 30px"><table class="table table-responsive"><tr><td><b>Дата занятия</b></td><td><b>Тема занятия</b></td><td><b>Форма контроля</b></td><td><b>ФИО педагога</b></td></tr>';
    foreach ($lessons as $lesson)
    {
        $theme = \app\models\work\LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->one();
        $result = '';
        if ($theme !== null) $result = $theme->theme;
        echo '<tr><td>'.date("d.m.Y", strtotime($lesson->lesson_date)).'</td>
             <td>'.$result.'</td><td>'.$theme->controlType->name.'</td><td>'.$theme->teacherWork->shortName.'</td></tr>';
    }
    echo '</table></div>';
?>
