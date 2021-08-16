<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\JournalModel */

$this->title = 'Электронный журнал';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    function changeColor(obj)
    {
        if (obj.value == 0) obj.style.background = "green";
        if (obj.value == 1) obj.style.background = "#DC143C";
        if (obj.value == 2) obj.style.background = "#183BD9";
        if (obj.value == 3) obj.style.background = "white";
    }

    function allAdd(obj)
    {
        var lessons = document.getElementsByClassName("class" + obj);

        for (let i = 0; i < lessons.length; i++) {
            lessons[i].value = "0";
            lessons[i].style.background = "green";
        }
    }

    function allClear(obj)
    {
        var lessons = document.getElementsByClassName("class" + obj);

        for (let i = 0; i < lessons.length; i++) {
            lessons[i].value = "3";
            lessons[i].style.background = "white";
        }
    }
</script>


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

<div>
    <?php
    echo Html::a("Переключиться в режим просмотра", \yii\helpers\Url::to(['journal/index', 'group_id' => $model->trainingGroup]), ['class'=>'btn btn-success'])
    ?>
</div>
<?php
$parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
$lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();
$form = ActiveForm::begin();
$counter = 0;
echo '<br><h4>Журнал посещений (Я<i> - явка, </i>Н<i> - неявка, </i>Д<i> - дистант)</i></h4>';
echo '<div class="containerTable">';
echo '<table class="table table-bordered"><thead><tr>';
echo '<th style="vertical-align: middle;">ФИО ученика / Даты занятий</th>';
$c = 0;
foreach ($lessons as $lesson)
{
    echo $form->field($model, 'lessons[]')->hiddenInput(['value'=> $lesson->id])->label(false);
    echo "<th>".date("d.m", strtotime($lesson->lesson_date)).'<br><a href="#" onclick="return allAdd('.$c.');" class="btn btn-success" style="margin-bottom: 5px">Все Я</a><a href="#" onclick="return allClear('.$c.');" class="btn btn-default">Все --</a>'."</th>";
    $c++;
}
echo '</thead><tbody>';
foreach ($parts as $part)
{
    echo '<tr>';
    echo '<th style="text-align: left;">' . $part->participantWork->shortName . "</th>";
    echo $form->field($model, 'participants[]')->hiddenInput(['value'=> $part->participant_id])->label(false);
    $c = 0;
    foreach ($lessons as $lesson)
    {
        $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$counter]])->one();
        $value = false;
        $dis = false;
        $date = new DateTime(date("Y-m-d"));
        $date->modify('-1 week');
        if (!($visits == null || $visits->status == 0)) $value = true;
        if (\app\models\components\UserRBAC::IsAccess(Yii::$app->user->getId(), 23) || \app\models\components\UserRBAC::IsAccess(Yii::$app->user->getId(), 25)) $dis = false;
        $selected0 = $visits->status == 0 ? 'selected' : '';
        $selected1 = $visits->status == 1 ? 'selected' : '';
        $selected2 = $visits->status == 2 ? 'selected' : '';
        $selected3 = $visits->status == 3 ? 'selected' : '';
        $color = 'style="background: white"';
        if ($visits->status == 0) $color = 'style="background: green; color: white; appearance: none;-webkit-appearance: none;"';
        if ($visits->status == 1) $color = 'style="background: #DC143C; color: white; appearance: none;-webkit-appearance: none;"';
        if ($visits->status == 2) $color = 'style="background: #183BD9; color: white; appearance: none;-webkit-appearance: none;"';
        if ($visits->status == 3) $color = 'style="background: white; color: white; appearance: none;-webkit-appearance: none;"';
        echo "<td style='padding: 5px 5px 0 5px;'>";
        $disabledStr = $dis ? 'disabled' : '';
        if (!$dis) echo $form->field($model, 'visits_id[]', ['template' => "{input}", 'options' => ['class' => 'form-inline']])->hiddenInput(['value' => $visits->id])->label(false);
        echo '<select class="form-control class'.$c.'" '.$disabledStr.' onchange="changeColor(this)" id="journalmodel-visits" class="form-control" name="JournalModel[visits][]" '.$color.'>';
        echo '<option value="3" '.$selected3.' style="background: white">--</option>';
        echo '<option style="background: green; color: white" value="0" '.$selected0.'>Я</option>';
        echo '<option style="background: #DC143C; color: white" value="1" '.$selected1.'>Н</option>';
        echo '<option style="background: #183BD9; color: white" value="2" '.$selected2.'>Д</option>';
        echo '</select></td>';
        $c++;
        $counter++;
    }
    echo '</tr>';
}
echo '</tbody></table></div><br>';

echo '<h4>Тематический план занятий</h4><br>';
echo '<table class="table table-responsive"><tr><td><b>Дата занятия</b></td><td><b>Тема занятия</b></td><td><b>ФИО педагога</b></td></tr>';
foreach ($lessons as $lesson)
{
    $teachers = \app\models\work\TeacherGroupWork::find()->where(['training_group_id' => $model->trainingGroup])->all();
    $teachers_id = [];
    foreach ($teachers as $teacher)
        $teachers_id[] = $teacher->teacher_id;
    $people = \app\models\work\PeopleWork::find()->where(['in', 'id', $teachers_id])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    $theme = \app\models\work\LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->one();
    $value = '';
    if ($theme !== null) $value = $theme->theme;
    echo '<tr><td>'.date("d.m.Y", strtotime($lesson->lesson_date)).'</td><td>'.
        $form->field($model, 'themes[]')->textInput(['value' => $value])->label(false).'</td><td>'.
        $form->field($model, "teachers[]")->dropDownList($items,$params)->label(false).
        '</td></tr>';
}
echo '</table>';
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
?>
