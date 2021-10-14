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

    let wdt = 600;

    function resize(type) {
        var table = document.getElementById("tableId");
        if(table) {
            if (type == 1) {
                if (wdt < 600)
                    wdt += 100;
            }else {
                if (wdt > 200)
                    wdt -= 100;
            }
            table.style.height = wdt + "px";
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

    .content-blocker {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(136, 136, 204, 0.5);
        z-index: 4444;
        text-align: center;
        display: flex;
        flex-flow: row wrap;
        justify-content: center;
        align-items: center;
    }

    .md-modal {
        margin: auto;
        position: fixed;
        top: 100px;
        left: 0;
        right: 0;
        width: 50%;
        max-width: 630px;
        min-width: 320px;
        height: auto;
        z-index: 2000;
        visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        backface-visibility: hidden;
    }

    .md-show {
        visibility: visible;
    }

    .md-overlay {
        position: fixed;
        width: 100%;
        height: 100%;
        visibility: hidden;
        top: 0;
        left: 0;
        z-index: 1000;
        opacity: 0;
        background: rgba(#e4f0e3, 0.8);
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        transition: all 0.3s;
    }

    .md-show ~ .md-overlay {
        opacity: 1;
        visibility: visible;
    }

    .md-effect-12 .md-content {
        -webkit-transform: scale(0.8);
        -moz-transform: scale(0.8);
        -ms-transform: scale(0.8);
        transform: scale(0.8);
        opacity: 0;
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        transition: all 0.3s;
    }

    .md-show.md-effect-12 ~ .md-overlay {
        background-color: #e4f0e3;
    }

    .md-effect-12 .md-content h3,
    .md-effect-12 .md-content {
        background: transparent;
    }

    .md-show.md-effect-12 .md-content {
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);
        opacity: 1;
    }

    .image-holder {
        position:absolute;
        left: 50%;
        top: 50%;
        width: 100px;
        height: 100px;
    }

    .image-holder img
    {
        width: 100%;
        margin-left: -50%;
        margin-top: -50%;
    }

</style>

<div>
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

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td align="left" style="text-align: left; font-family: Tahoma; font-size: 20px; padding-left: 0">Журнал посещений (Я<i> - явка, </i>Н<i> - неявка, </i>Д<i> - дистант)</i></td>';
    echo '<td align="right" style="text-align: right; font-family: Tahoma; font-size: 20px; padding-: 0">Масштабирование журнала: <a class="btn btn-success" onclick="resize(1)" style="margin-right: 10px; width: 40px; font-size: 18px">+</a><a onclick="resize(2)" class="btn btn-danger" style="width: 40px; font-size: 18px">-</a></td>';
    echo '</tr></table>';
    echo '<div class="containerTable" id="tableId">';
    echo '<table class="table table-bordered"><thead><tr>';
    echo '<th style="vertical-align: middle;">ФИО ученика / Даты занятий</th>';
    $c = 0;
    foreach ($lessons as $lesson)
    {
        echo $form->field($model, 'lessons[]')->hiddenInput(['value'=> $lesson->id])->label(false);
        echo "<th>".date("d.m", strtotime($lesson->lesson_date)).'<br><a onclick="return allAdd('.$c.');" class="btn btn-success" style="margin-bottom: 5px">Все Я</a><a onclick="return allClear('.$c.');" class="btn btn-default">Все --</a>'."</th>";
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
            /*вот тут должна быть проверка на дату и если не заполнил журнал за неделю - идёшь лесом, а не редактирование*/
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
    echo '<div style="overflow-y: scroll; max-height: 400px; margin-bottom: 30px"><table class="table table-responsive"><tr><td><b>Дата занятия</b></td><td><b>Тема занятия</b></td><td><b>ФИО педагога</b></td></tr>';
    foreach ($lessons as $lesson)
    {
        $teachers = \app\models\work\TeacherGroupWork::find()->where(['training_group_id' => $model->trainingGroup])->all();
        $teachers_id = [];
        foreach ($teachers as $teacher)
            $teachers_id[] = $teacher->teacher_id;
        $people = \app\models\work\PeopleWork::find()->where(['in', 'id', $teachers_id])->all();
        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
        $theme = \app\models\work\LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->one();
        $params = [
            'options' => [$theme->teacher_id => ['Selected' => true]],
        ];
        $value = '';
        if ($theme !== null) $value = $theme->theme;
        echo '<tr><td>'.date("d.m.Y", strtotime($lesson->lesson_date)).'</td><td>'.
            $form->field($model, 'themes[]')->textInput(['value' => $value])->label(false).'</td><td>'.
            $form->field($model, "teachers[]")->dropDownList($items,$params)->label(false).
            '</td></tr>';
    }
    echo '</table></div>';
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary md-trigger', 'data-modal' => 'modal-12']);
    ActiveForm::end();
    ?>

    <div class="md-modal md-effect-12">
        <div class="content-blocker">
            <div style="border-radius: 10px; margin-bottom: 200px; font-size: 24px; background: whitesmoke; padding: 5px 5px 5px 5px">
                Пожалуйста, подождите. Данные обновляются...
            </div>
            <div class="image-holder">
                <img src="load.gif"/>
            </div>
        </div>
    </div>
</div>

<?php
$js2 =<<< JS
    $(".md-trigger").on('click', function() {
        $(".md-modal").addClass('md-show');
    });

    $(".md-close").on('click', function() {
        $(".md-modal").removeClass("md-show");
    })
JS;

$this->registerJs($js2, \yii\web\View::POS_LOAD);

?>
