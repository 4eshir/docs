<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

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
            if (lessons[i].parentNode.parentNode.style.background !== 'rgb(145, 138, 138) none repeat scroll 0% 0%' && lessons[i].parentNode.parentNode.style.background !== 'rgb(145, 138, 138)')
            {
                console.log(lessons[i].parentNode.parentNode.style.background);
                lessons[i].value = "0";
                lessons[i].style.background = "green";
            }
            else
                console.log(lessons[i].parentNode.parentNode.style.background);
        }
    }

    function allClear(obj)
    {
        var lessons = document.getElementsByClassName("class" + obj);

        for (let i = 0; i < lessons.length; i++) {
            if (lessons[i].parentNode.parentNode.style.background !== 'rgb(145, 138, 138) none repeat scroll 0% 0%' && lessons[i].parentNode.parentNode.style.background !== 'rgb(145, 138, 138)')
            {
                lessons[i].value = "3";
                lessons[i].style.background = "white";
            }
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

    #button-design .b-green,#button-design .b-green:before{background:#499bea;background:-moz-linear-gradient(45deg,#499bea 0,#1abc9c 100%);background:-webkit-gradient(left bottom,right top,color-stop(0,#499bea),color-stop(100%,#1abc9c));background:-webkit-linear-gradient(45deg,#499bea 0,#1abc9c 100%);background:-o-linear-gradient(45deg,#499bea 0,#1abc9c 100%);background:-ms-linear-gradient(45deg,#499bea 0,#1abc9c 100%);background:linear-gradient(45deg,#499bea 0,#1abc9c 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#499bea',endColorstr='#1abc9c',GradientType=1)}
    #button-design .button2{display:inline-block;font-size:16px;margin:2px;padding:.5em;border-radius:5px;transition:all .5s;filter:hue-rotate(0);color:#FFF;text-decoration:none}
    #button-design .rot-135:hover{filter:hue-rotate(135deg)}

</style>

<div>
    <div class="form-group col-xs-5" style="padding-top: 1.75em;">
        <?php
        echo Html::a("Переключиться в режим просмотра", \yii\helpers\Url::to(['journal/index', 'group_id' => $model->trainingGroup]), ['class'=>'btn btn-success']);
        ?>
    </div>

    <?php
    $parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC, 'participant.firstname' => SORT_ASC, 'participant.patronymic' => SORT_ASC])->all();
    $lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();
    $form = ActiveForm::begin(['id' => 'dynamic-form']);
    //echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary md-trigger', 'data-modal' => 'modal-12', 'style' => "margin-left: 55em; margin-top: -4em;"]);
    $counter = 0;

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td align="left" style="text-align: left; font-family: Tahoma; font-size: 20px; padding-left: 0">Журнал посещений (Я<i> - явка, </i>Н<i> - неявка, </i>Д<i> - дистант)</i></td>';
    echo '<td align="right" style="text-align: right; font-family: Tahoma; font-size: 20px; padding-left: 0">Масштабирование журнала: <a class="btn btn-success" onclick="resize(1)" style="margin-right: 10px; width: 40px; font-size: 18px">+</a><a onclick="resize(2)" class="btn btn-danger" style="width: 40px; font-size: 18px">-</a></td>';
    echo '</tr></table>';
    echo '<div class="containerTable" id="tableId">';
    echo '<table class="table table-bordered"><thead><tr>';
    echo '<th style="vertical-align: middle;">ФИО ученика / Даты занятий</th>';
    $c = 0;
    foreach ($lessons as $lesson)
    {
        echo $form->field($model, 'lessons[]')->hiddenInput(['value'=> $lesson->id])->label(false);
        $dis = true;
        $date = new DateTime(date("Y-m-d"));
        $date->modify('-10 days');
        $roles = [5, 6, 7];
        $group = \app\models\work\TrainingGroupWork::find()->where(['id' => $model->trainingGroup])->one();
        $isMethodist = \app\models\work\UserRoleWork::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'role_id', $roles])->one();
        $isToken = \app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 49);
        if (($isMethodist || $isToken || $lesson->lesson_date >= $date->format('Y-m-d')) && !$group->archive == 1) $dis = false;
        if (!$dis)
            echo "<th>".date("d.m", strtotime($lesson->lesson_date)).'<br><a onclick="return allAdd('.$c.');" class="btn btn-success" style="margin-bottom: 5px">Все Я</a><a onclick="return allClear('.$c.');" class="btn btn-default">Все --</a>'."</th>";
        else
            echo "<th>".date("d.m", strtotime($lesson->lesson_date)).'<br><a disabled class="btn btn-success" style="margin-bottom: 5px">Все Я</a><a disabled class="btn btn-default">Все --</a>'."</th>";
        $c++;
    }
    echo '<th style="vertical-align: middle;">Тема проекта</th>';
    echo '<th style="vertical-align: middle;">Оценка</th>';
    echo '<th style="vertical-align: middle;">Успешное завершение</th>';
    echo '</thead><tbody>';
    foreach ($parts as $part)
    {
        if ($part->status == 1 || $part->status == 2)
            echo '<tr style="background:#918a8a">';
        else
            echo '<tr>';
        echo '<th style="text-align: left; background: white;">' . $part->participantWork->shortName . "</th>";
        echo $form->field($model, 'participants[]')->hiddenInput(['value'=> $part->participant_id])->label(false);
        $c = 0;

        foreach ($lessons as $lesson)
        {
            $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$counter]])->one();
            $value = false;
            $dis = true;
            $date = new DateTime(date("Y-m-d"));
            $date->modify('-10 days');
            $roles = [5, 6, 7];
            $group = \app\models\work\TrainingGroupWork::find()->where(['id' => $model->trainingGroup])->one();

            $isMethodist = \app\models\work\UserRoleWork::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'role_id', $roles])->one();
            $isToken = \app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 49);
            if (!($visits == null || $visits->status == 0)) $value = true;
            /*вот тут должна быть проверка на дату и если не заполнил журнал за неделю - идёшь лесом, а не редактирование*/
            if (($isMethodist != null || $isToken || $lesson->lesson_date >= $date->format('Y-m-d') && $part->status == 0) && !$group->archive == 1) $dis = false;
            //echo ($isMethodist || $isToken || $lesson->lesson_date >= $date->format('Y-m-d') || $part->status == 1) && !$group->archive == 1;isits->status == 0 ? 'selected' : '';
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

        $themes = \app\models\work\GroupProjectThemesWork::find()->where(['training_group_id' => $model->trainingGroup])->all();
        $tId = [];
        foreach ($themes as $theme) $tId[] = $theme->theme_id;

        $themes = \app\models\work\ProjectThemeWork::find()->where(['IN', 'id', $tId])->all();

        $items = \yii\helpers\ArrayHelper::map($themes,'id','name');
        $params = [
            'prompt' => '--',
            'style' => 'min-width: 200px'
        ];

        echo '<td>'.$form->field($model, "projectThemes[]")->dropDownList($items,$params)->label(false).'</td>';
        echo '<td>'.$form->field($model, 'cwPoints[]')->textInput(['type' => 'number', 'value' => $part->points, $group->archive ? 'disabled' : '' => '', 'style' => 'min-width: 70px'])->label(false).'</td>';
        if ($part->success == 1)
            echo '<td>'.$form->field($model, 'successes[]')->checkbox([$group->archive ? 'disabled' : '' => '', 'checked' => 'checked', 'label' => null, 'value' => $part->id,]).$form->field($model, 'tpIds[]')->hiddenInput(['value' => $part->id])->label(false).'</td>';
        else
            echo '<td>'.$form->field($model, 'successes[]')->checkbox([$group->archive ? 'disabled' : '' => '', 'label' => null, 'value' => $part->id,]).$form->field($model, 'tpIds[]')->hiddenInput(['value' => $part->id])->label(false).'</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div><br>';
    $group = \app\models\work\TrainingGroupWork::find()->where(['id' => $model->trainingGroup])->one();
    //echo '<h4>Тематический план занятий</h4><br>';
    echo '<table>';
    echo '<tr>';
    echo '<td align="left" style="width: 77.5%; text-align: left; font-family: Tahoma; font-size: 20px; padding-left: 0">Тематический план занятий</td>';
    //echo '<td align="right" style="text-align: right; font-family: Tahoma;  font-size: 20px; "><div id="button-design" style="margin: 0 0 0 auto;">'.Html::a('Очистить тематический план', \yii\helpers\Url::to(['journal/lesson-theme-clear', 'group_id' => $model->trainingGroup]), ['class' => 'button2 b-green rot-135']).'</div></td>';
    echo '<td align="right" style="text-align: right; font-family: Tahoma;  font-size: 20px; "><div id="button-design" style="margin: 0 0 0 auto;">';
    \yii\bootstrap\Modal::begin([
        'header' => '<p style="text-align: left; font-weight: 700; color: #f0ad4e;">Предупреждение</p>',
        'toggleButton' => ['label' => 'Очистить тематический план', 'class' => 'btn btn-secondary', $group->archive ? 'disabled' : '' => ''],
    ]);
    echo '<p style="text-align: left">Внимание, очистка тематического плана приведет к ПОЛНОЙ очистке внесенных тем и ФИО педагога!</p>';
    echo '<table><tr><td style="text-align: right; width: 50%;>"';
    echo '<div id="button-design" style="margin: 0 0 0 auto;">'.Html::a('Очистить тематический план', \yii\helpers\Url::to(['journal/lesson-theme-clear', 'group_id' => $model->trainingGroup]), ['class' => 'button2 b-green rot-135']).'</div>';
    echo '</td><td style="text-align: left;">';
    echo '<button style="border: 3px solid red; padding: 7px 7px 7px 7px; border-radius: 8px; color: red" type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-ban-circle"></span> Отмена</button>';
    echo '</td></tr></table>';
    \yii\bootstrap\Modal::end();
    echo '</div></td></tr></table>';
    echo '<div style="overflow-y: scroll; max-height: 400px; margin-bottom: 30px"><table class="table table-responsive"><tr><td><b>Дата занятия</b></td><td style="width: 40%"><b>Тема занятия</b></td><td><b>Форма контроля</b></td><td><b>ФИО педагога</b></td></tr>';

    foreach ($lessons as $lesson)
    {
        $teachers = \app\models\work\TeacherGroupWork::find()->where(['training_group_id' => $model->trainingGroup])->all();
        $teachers_id = [];
        foreach ($teachers as $teacher)
            $teachers_id[] = $teacher->teacher_id;
        $people = \app\models\work\PeopleWork::find()->where(['in', 'id', $teachers_id])->all();
        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
        $control = \app\models\work\ControlTypeWork::find()->all();
        $items2 = \yii\helpers\ArrayHelper::map($control,'id','name');

        $theme = \app\models\work\LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->one();
        $params = [
            'options' => [$theme->teacher_id => ['Selected' => true]],
            $group->archive ? 'disabled' : '' => '',
        ];
        $params2 = [
            'options' => [$theme->control_type_id => ['Selected' => true]],
            $group->archive ? 'disabled' : '' => '',
        ];
        $value = '';
        if ($theme !== null) $value = $theme->theme;
        echo '<tr><td>'.date("d.m.Y", strtotime($lesson->lesson_date)).'</td><td>'.
            $form->field($model, 'themes[]')->textInput(['value' => $value, $group->archive ? 'disabled' : '' => ''])->label(false).'</td><td>'.
            $form->field($model, "controls[]")->dropDownList($items2,$params2)->label(false).'</td><td>'.
            $form->field($model, "teachers[]")->dropDownList($items,$params)->label(false).
            '</td></tr>';
    }
    echo '</table></div>';?>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><p style="width: 77.5%; text-align: left; font-family: Tahoma; font-size: 20px; padding-left: 0">Темы проектов</p></div>
            <?php
            $themes = \app\models\work\GroupProjectThemesWork::find()->where(['training_group_id' => $model->trainingGroup])->all();
            if ($themes != null)
            {
                echo '<table>';
                foreach ($themes  as $theme) {
                    echo '<tr><td style="padding-left: 20px"><h4>"'.$theme->projectTheme->name.'"</h4></td> <td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['event/delete-external-event', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelProjectThemes[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items1" ><!-- widgetContainer -->
                    <?php foreach ($modelProjectThemes as $i => $modelProjectTheme): ?>
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div>
                                    <?php

                                    $branch = \app\models\work\EventExternalWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelProjectTheme, "[{$i}]themeName")->textInput($items,$params)->label('Тема проекта');
                                    ?>

                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>
    <?php 
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
