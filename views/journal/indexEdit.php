<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\JournalModel */

$this->title = 'Участники мероприятий';
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
</script>

<style>
    select:focus {outline:none;}
</style>

<div>
    <?php
    echo Html::a("Переключиться в режим просмотра", \yii\helpers\Url::to(['journal/index', 'group_id' => $model->trainingGroup]), ['class'=>'btn btn-success'])
    ?>
</div>
<?php
    $parts = \app\models\common\TrainingGroupParticipant::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
    $lessons = \app\models\common\TrainingGroupLesson::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
    $form = ActiveForm::begin();
    $counter = 0;

    echo '<br><h4>Журнал посещений (Я<i> - явка, </i>Н<i> - неявка, </i>Д<i> - дистант)</i></h4><table class="table table-bordered">';
    echo '<tr><td>ФИО ученика / Даты занятий</td>';
    foreach ($lessons as $lesson)
    {
        echo $form->field($model, 'lessons[]')->hiddenInput(['value'=> $lesson->id])->label(false);
        echo "<td>".date("d.m", strtotime($lesson->lesson_date))."</td>";
    }
    echo '</tr>';
    foreach ($parts as $part)
    {
        $tr = '<tr>';
        if ($part->status == 1)
            $tr = '<tr style="background:lightcoral">';
        echo $tr.'<td style="padding: 5px 0 0 10px">'.$part->participant->shortName.'</td>';
        echo $form->field($model, 'participants[]')->hiddenInput(['value'=> $part->participant_id])->label(false);
        foreach ($lessons as $lesson)
        {
            $visits = \app\models\common\Visit::find()->where(['training_group_lesson_id' => $lesson->id])->andWhere(['foreign_event_participant_id' => $part->participant->id])->one();
            $value = false;
            $dis = false;
            $date = new DateTime(date("Y-m-d"));
            $date->modify('-1 week');
            if (!($visits == null || $visits->status == 0)) $value = true;
            if ($lesson->lesson_date < $date->format('Y-m-d') || $lesson->lesson_date > date("Y-m-d")) $dis = true;
            if ($part->status == 1) $dis = true;
            $selected0 = $model->visits[$counter] == 0 ? 'selected' : '';
            $selected1 = $model->visits[$counter] == 1 ? 'selected' : '';
            $selected2 = $model->visits[$counter] == 2 ? 'selected' : '';
            $selected3 = $model->visits[$counter] == 3 ? 'selected' : '';
            $color = 'style="background: white"';
            if ($model->visits[$counter] == 0) $color = 'style="background: green; color: white"';
            if ($model->visits[$counter] == 1) $color = 'style="background: #DC143C; color: white"';
            if ($model->visits[$counter] == 2) $color = 'style="background: #183BD9; color: white"';
            if ($model->visits[$counter] == 3) $color = 'style="background: white; color: white"';
            echo "<td style='padding: 5px 5px 0 5px'>";
            $disabledStr = $dis ? 'disabled' : '';
            echo '<select '.$disabledStr.' onchange="changeColor(this)" id="journalmodel-visits" class="form-control" name="JournalModel[visits][]"'.$color.'>';
            echo '<option value="3" '.$selected0.' style="background: white">--</option>';
            echo '<option style="background: green; color: white" value="0" '.$selected0.'>Я</option>';
            echo '<option style="background: #DC143C; color: white" value="1" '.$selected1.'>Н</option>';
            echo '<option style="background: #183BD9; color: white" value="2" '.$selected2.'>Д</option>';
            echo '</select></td>';

            //echo "<td style='padding: 5px 5px 0 5px'>".$form->field($model, 'visits[]', ['options' => ['display' => 'block']])->dropDownList([3 => '--', 0 => 'Я',
            //        1 => 'Н', 2 => 'Д'], ['options' => [$model->visits[$counter] => ['Selected' => true]], 'disabled' => $dis ? true : false, 'style' => 'background: blue'])->label(false)."</td>";
            $counter++;
        }
        echo '</tr>';
    }
    echo '</table><br><br>';
    echo '<h4>Тематический план занятий</h4><br>';
    echo '<table class="table table-responsive"><tr><td><b>Дата занятия</b></td><td><b>Тема занятия</b></td></tr>';
    foreach ($lessons as $lesson)
    {
        $theme = \app\models\common\LessonTheme::find()->where(['training_group_lesson_id' => $lesson->id])->one();
        $value = '';
        if ($theme !== null) $value = $theme->theme;
        echo '<tr><td>'.date("d.m.Y", strtotime($lesson->lesson_date)).'</td><td>'.$form->field($model, 'themes[]')->textInput(['value' => $value])->label(false).'</td></tr>';
    }
    echo '</table>';
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    ActiveForm::end();
?>
