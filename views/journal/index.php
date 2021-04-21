<?php

use app\models\common\TrainingGroup;
use app\models\common\User;
use app\models\components\UserRBAC;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\JournalModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Участники мероприятий';
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
    $parts = \app\models\common\TrainingGroupParticipant::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
    $lessons = \app\models\common\TrainingGroupLesson::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
    $user = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
    $form = ActiveForm::begin(); ?>
    <?php
    $groups = TrainingGroup::find()->where(['teacher_id' => $user->aka])->all();
    if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 22)) //доступ на просмотр ВСЕХ групп
    {
        $groups = TrainingGroup::find()->all();
    }
    else if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 24)) //доступ на просмотр групп СВОЕГО ОТДЕЛА
    {
        $branchs = \app\models\common\PeoplePositionBranch::find()->select('branch_id')->distinct()->where(['people_id' => $user->aka])->all();
        if ($branchs !== null)
        {
            $branchs_id = [];
            foreach ($branchs as $branch) $branchs_id[] = $branch->branch_id;
            $groups_id = \app\models\common\TrainingGroupLesson::find()->select('training_group_id')->distinct()->where(['in', 'branch_id', $branchs_id])->all();
            $newGroups_id = [];
            foreach ($groups_id as $group_id) $newGroups_id[] = $group_id->training_group_id;
            $groups = TrainingGroup::find()->where(['in', 'id', $newGroups_id])->all();
        }
    }
    else
    {
        $teachers = \app\models\common\TeacherGroup::find()->select('training_group_id')->distinct()->where(['teacher_id' => $user->aka])->all();
        $teachers_id = [];
        foreach ($teachers as $teacher) $teachers_id[] = $teacher->training_group_id;
        $groups = TrainingGroup::find()->where(['in', 'id', $teachers_id])->all();
    }
    if ($groups !== null)
        $items =  \yii\helpers\ArrayHelper::map($groups,'id','number');
    else
    {
        $tgroups = \app\models\common\TeacherGroup::find()->where(['teacher_id' => $user->aka])->all();
        $tgroups = \yii\helpers\ArrayHelper::map($tgroups, 'id', 'training_group_id');
        $groups = TrainingGroup::find()->where(['in', 'id', $tgroups])->all();
        $items = \yii\helpers\ArrayHelper::map($groups, 'id', 'number');
    }
    $params = [
        'prompt' => '',
    ];
    echo '<div class="col-xs-3">';
    echo $form->field($model, 'trainingGroup')->dropDownList($items,$params)->label('Группа №');
    echo '</div>';
    ?>
    <div class="form-group col-xs-4">
        <?= Html::submitButton('Показать расписание', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
<div>
    <?php
    echo Html::a("Переключиться в режим редактирования", \yii\helpers\Url::to(['journal/index-edit', 'group_id' => $model->trainingGroup]), ['class'=>'btn btn-success'])
    ?>
</div>

<?php
    echo '<table class="table table-bordered">';
    echo '<tr><td>ФИО ученика / Даты занятий</td>';
    foreach ($lessons as $lesson)
    {
        echo "<td>".date("d.m", strtotime($lesson->lesson_date))."</td>";
    }
    echo '</tr>';
    foreach ($parts as $part)
    {
        $tr = '<tr>';
        if ($part->status == 1)
            $tr = '<tr style="background:lightcoral">';
        echo $tr.'<td>'.$part->participant->shortName.'</td>';
        foreach ($lessons as $lesson)
        {
            $visits = \app\models\common\Visit::find()->where(['training_group_lesson_id' => $lesson->id])->andWhere(['foreign_event_participant_id' => $part->participant->id])->one();
            if ($visits == null)
                echo '<td>--</td>';
            else
                echo $visits->prettyStatus;
        }
        echo '</tr>';
    }
    echo '</table><br><br>';
    echo '<h4>Тематический план занятий</h4><br>';
    echo '<table class="table table-responsive"><tr><td><b>Дата занятия</b></td><td><b>Тема занятия</b></td><td><b>ФИО педагога</b></td></tr>';
    foreach ($lessons as $lesson)
    {
        $theme = \app\models\common\LessonTheme::find()->where(['training_group_lesson_id' => $lesson->id])->one();
        $result = '';
        if ($theme !== null) $result = $theme->theme;
        echo '<tr><td>'.date("d.m.Y", strtotime($lesson->lesson_date)).'</td>
             <td>'.$result.'</td><td>'.$theme->teacher->shortName.'</td></tr>';
    }
    echo '</table>';
?>
