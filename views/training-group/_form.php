<?php

use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupWork */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$js =<<< JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        var d = new Date();
        var elems = document.getElementsByClassName('def');
        elems[elems.length - 1].value = '10:00';
    });
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
?>

<?php
$session = Yii::$app->session;
?>

<div class="training-group-form">

    <?php echo Html::a('Показать общую информацию', \yii\helpers\Url::to(['training-group/show-common', 'modelId' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?php echo Html::a('Показать список учеников', \yii\helpers\Url::to(['training-group/show-parts', 'modelId' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?php echo Html::a('Показать расписание', \yii\helpers\Url::to(['training-group/show-schedule', 'modelId' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <div style="height: 20px"></div>
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div id="common" <?php echo $session->get("show") === "common" ? '' : 'hidden'; ?>>
    <?= $form->field($model, 'budget')->checkbox() ?>

    <?php
    $progs = \app\models\work\TrainingProgramWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($progs,'id','name');
    $params = [
    ];
    echo $form->field($model, 'training_program_id')->dropDownList($items,$params);

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Педагогический состав</h4></div>
            <div>
                <?php
                $teachers = \app\models\work\TeacherGroupWork::find()->where(['training_group_id' => $model->id])->all();
                if ($teachers != null)
                {
                    echo '<table class="table table-bordered">';
                    echo '<tr><td><b>ФИО педагога</b></td></tr>';
                    foreach ($teachers as $teacher) {
                            echo '<tr><td><h5>'.$teacher->teacherWork->shortName.'</h5></td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-teacher', 'id' => $teacher->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper5', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items5', // required: css class selector
                    'widgetItem' => '.item5', // required: css class
                    'limit' => 4, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelTeachers[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items5" ><!-- widgetContainer -->
                    <?php foreach ($modelTeachers as $i => $modelTeacher): ?>
                        <div class="item5 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Педагог</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (!$modelTeacher->isNewRecord) {
                                    echo Html::activeHiddenInput($modelTeacher, "[{$i}]id");
                                }
                                ?>
                                <div class="col-xs-4">
                                    <?php
                                    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelTeacher, "[{$i}]teacher_id")->dropDownList($items,$params)->label("ФИО педагога");

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

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Приказы по группе</h4></div>
            <div>
                <?php
                $orders = \app\models\work\OrderGroupWork::find()->where(['training_group_id' => $model->id])->all();
                if ($orders != null)
                {
                    echo '<table class="table table-bordered">';
                    echo '<tr><td><b>Номер и название приказа</b></td><td></td></tr>';
                    foreach ($orders as $order) {
                        echo '<tr><td><h5>'.$order->documentOrderWork->fullName.'</h5></td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-order', 'id' => $order->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items4', // required: css class selector
                    'widgetItem' => '.item4', // required: css class
                    'limit' => 100, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item4', // css class
                    'deleteButton' => '.remove-item4', // css class
                    'model' => $modelOrderGroup[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items4" ><!-- widgetContainer -->
                    <?php foreach ($modelOrderGroup as $i => $modelOrderGroupOne): ?>
                        <div class="item4 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Приказ</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item4 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item4 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelOrderGroupOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelOrderGroupOne, "[{$i}]id");
                                }
                                ?>
                                <div class="col-xs-4">
                                    <?php
                                    $params = [
                                        'prompt' => '',
                                    ];

                                    $orders = \app\models\work\DocumentOrderWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');

                                    echo $form->field($modelOrderGroupOne, "[{$i}]document_order_id")->dropDownList($items,$params);

                                    ?>
                                </div>
                                <div class="col-xs-4">
                                    <?= $form->field($modelOrderGroupOne, "[{$i}]comment")->textInput(); ?>
                                </div>


                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата начала занятий',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]]) ?>

    <?= $form->field($model, 'finish_date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания занятий',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]]) ?>

    <?= $form->field($model, 'photosFile[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->photos) > 2)
    {
        $split = explode(" ", $model->photos);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['training-group/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'photos'])).'</td></tr>';
        }
        echo '</table>';
    }
    echo '<br>';
    ?>


    <?= $form->field($model, 'presentDataFile[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->present_data) > 2)
    {
        $split = explode(" ", $model->present_data);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'type' => 'present_data'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['training-group/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'present_data'])).'</td></tr>';
        }
        echo '</table>';
    }
    echo '<br>';
    ?>

    <?= $form->field($model, 'workDataFile[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->work_data) > 2)
    {
        $split = explode(" ", $model->work_data);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'type' => 'work_data'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['training-group/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'work_data'])).'</td></tr>';
        }
        echo '</table>';
    }
    echo '<br>';
    ?>
    </div>

    <div id="parts" <?php echo $session->get("show") === "parts" ? '' : 'hidden'; ?>>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Состав</h4></div>
                <div style="padding-left: 1.5%; padding-top: 1%">
                    <?= $form->field($model, 'fileParticipants')->fileInput() ?>
                </div>
                <div>
                    <?php
                    $extEvents = \app\models\work\TrainingGroupParticipantWork::find()->where(['training_group_id' => $model->id])->all();
                    if ($extEvents != null)
                    {
                        echo '<table class="table table-bordered">';
                        echo '<tr><td><b>ФИО</b></td><td><b>Номер сертификата</b></td><td><b>Способ доставки</b></td></tr>';
                        foreach ($extEvents  as $extEvent) {
                            if ($extEvent->status == 0)
                                echo '<tr><td><h5>'.$extEvent->participantWork->fullName.'</h5></td><td><h5>'.$extEvent->certificat_number.'</h5></td><td><h5>'.$extEvent->sendMethod->name.'</h5></td><td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-participant', 'id' => $extEvent->id]), ['class' => 'btn btn-primary']).'</td>'.
                                    '<td>&nbsp;'.Html::a('Отчислить', \yii\helpers\Url::to(['training-group/remand-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-warning']).'</td>'.
                                    '<td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                            else
                                echo '<tr style="background: lightcoral"><td><h5>'.$extEvent->participantWork->fullName.'</h5></td><td><h5>'.$extEvent->certificat_number.'</h5></td><td><h5>'.$extEvent->sendMethod->name.'</h5></td><td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-participant', 'id' => $extEvent->id]), ['class' => 'btn btn-primary']).'</td>'.
                                    '<td>&nbsp;'.Html::a('Восстановить', \yii\helpers\Url::to(['training-group/unremand-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-success']).'</td>'.
                                    '<td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                        }
                        echo '</table>';
                    }
                    ?>
                </div>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items1', // required: css class selector
                        'widgetItem' => '.item1', // required: css class
                        'limit' => 100, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $modelTrainingGroupParticipant[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'eventExternalName',
                        ],
                    ]); ?>

                    <div class="container-items1" ><!-- widgetContainer -->
                        <?php foreach ($modelTrainingGroupParticipant as $i => $modelTrainingGroupParticipantOne): ?>
                            <div class="item1 panel panel-default"><!-- widgetBody -->
                                <div class="panel-heading">
                                    <h3 class="panel-title pull-left">Учащийся</h3>
                                    <div class="pull-right">
                                        <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                        <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (! $modelTrainingGroupParticipantOne->isNewRecord) {
                                        echo Html::activeHiddenInput($modelTrainingGroupParticipantOne, "[{$i}]id");
                                    }
                                    ?>
                                    <div class="col-xs-4">
                                        <?php

                                        $people = \app\models\work\ForeignEventParticipantsWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                        $params = [
                                            'prompt' => '',
                                        ];
                                        echo $form->field($modelTrainingGroupParticipantOne, "[{$i}]participant_id")->dropDownList($items,$params)->label('ФИО учащегося');
                                        ?>

                                    </div>
                                    <div class="col-xs-4">
                                        <?= $form->field($modelTrainingGroupParticipantOne, "[{$i}]certificat_number")->textInput()->label('Номер сертификата') ?>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php
                                        $sendMethod= \app\models\work\SendMethodWork::find()->orderBy(['name' => SORT_ASC])->all();
                                        $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
                                        $params = [
                                            'prompt' => ''
                                        ];
                                        echo $form->field($modelTrainingGroupParticipantOne, "[{$i}]send_method_id")->dropDownList($items,$params)->label('Способ доставки');

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
    </div>

    <div id="schedule" <?php echo $session->get("show") === "schedule" ? '' : 'hidden'; ?>>

        <?= $form->field($model, 'schedule_type')->radioList(array('0' => 'Ручное заполнение расписания',
            '1' => 'Автоматическое расписание по дням'), ['value' => '0', 'name' => 'scheduleType', 'onchange' => 'checkSchedule()'])->label('') ?>

        <div id="manualSchedule">
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ручное заполнение расписания</h4></div>
                    <div>
                        <?php
                        $extEvents = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->id])->orderBy(['lesson_date' => SORT_ASC])->all();
                        if ($extEvents != null)
                        {
                            echo '<table class="table table-bordered">';
                            echo '<tr><td><b>Дата</b></td><td><b>Время начала</b></td><td><b>Время окончания</b></td><td><b>Помещение</b></td></tr>';
                            foreach ($extEvents as $extEvent) {
                                $class = 'default';
                                if (count($extEvent->checkValideTime($model->id)) > 0 || (strtotime($extEvent->lesson_end_time) - strtotime($extEvent->lesson_start_time)) / 60 < $extEvent->duration * 40 || $extEvent->lesson_date < $model->start_date || $extEvent->lesson_date > $model->finish_date) $class = 'danger';
                                echo '<tr class='.$class.'><td><h5>'.date('d.m.Y', strtotime($extEvent->lesson_date)).'</h5></td><td><h5>'.substr($extEvent->lesson_start_time, 0, -3).'</h5></td><td><h5>'.substr($extEvent->lesson_end_time, 0, -3).'</h5></td><td><h5>'.$extEvent->fullName.'</h5></td>'.
                                    '<td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-lesson', 'lessonId' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td><td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-lesson', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                            }
                            echo '</table>';
                        }
                        ?>
                    </div>
                    <div class="panel-body">
                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                            'widgetBody' => '.container-items2', // required: css class selector
                            'widgetItem' => '.item2', // required: css class
                            'limit' => 100, // the maximum times, an element can be cloned (default 999)
                            'min' => 1, // 0 or 1 (default 1)
                            'insertButton' => '.add-item2', // css class
                            'deleteButton' => '.remove-item2', // css class
                            'model' => $modelTrainingGroupLesson[0],
                            'formId' => 'dynamic-form',
                            'formFields' => [
                                'eventExternalName',
                            ],
                        ]); ?>

                        <div class="container-items2" ><!-- widgetContainer -->
                            <?php foreach ($modelTrainingGroupLesson as $i => $modelTrainingGroupLessonOne): ?>
                                <div class="item2 panel panel-default"><!-- widgetBody -->
                                    <div class="panel-heading">
                                        <h3 class="panel-title pull-left">Занятие</h3>
                                        <div class="pull-right">
                                            <button type="button" class="add-item2 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                            <button type="button" class="remove-item2 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        // necessary for update action.
                                        if (! $modelTrainingGroupLessonOne->isNewRecord) {
                                            echo Html::activeHiddenInput($modelTrainingGroupLessonOne, "[{$i}]id");
                                        }
                                        ?>
                                        <div class="col-xs-4">
                                            <?= $form->field($modelTrainingGroupLessonOne, "[{$i}]lesson_date")->textInput(['type' => 'date', 'id' => 'inputDate', 'class' => 'form-control inputDateClass'])->label('Дата занятия') ?>
                                        </div>
                                        <div class="col-xs-1">
                                            <?= $form->field($modelTrainingGroupLessonOne, "[{$i}]lesson_start_time")->textInput(['class' => 'form-control def', 'value' => '10:00'])->label('Начало занятия') ?>
                                        </div>
                                        <div class="col-xs-2">
                                            <?php
                                            //$branchs = \app\models\work\BranchWork::find()->all();
                                            //$items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
                                            $params = [
                                                'id' => $i,
                                                'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('subcat') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        var elems = document.getElementsByClassName("aud");
                                                        for (var c = 0; c !== elems.length; c++) {
                                                            if (elems[c].id == "r" + id)
                                                                elems[c].innerHTML = res;
                                                        }
                                                    }
                                                );
                                            ',
                                            ];

                                            $audits = \app\models\work\BranchWork::find()->orderBy(['name' => SORT_ASC])->all();
                                            $items = \yii\helpers\ArrayHelper::map($audits,'id','name');

                                            echo $form->field($modelTrainingGroupLessonOne, "[{$i}]auditorium_id")->dropDownList($items,$params)->label('Отдел');

                                            ?>

                                            <?php
                                            $params = [
                                                'prompt' => '',
                                                'id' => 'r'.$i,
                                                'class' => 'form-control aud',
                                            ];
                                            echo $form->field($modelTrainingGroupLessonOne, "[{$i}]auds")->dropDownList([], $params)->label('Помещение'); ?>
                                        </div>


                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php DynamicFormWidget::end(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="autoSchedule" hidden>
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Автоматическое заполнение расписания</h4></div>
                    <div>
                        <?php
                        $extEvents = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->id])->orderBy(['lesson_date' => SORT_ASC])->all();
                        if ($extEvents != null)
                        {
                            echo '<table class="table table-bordered">';
                            echo '<tr><td><b>Дата</b></td><td><b>Время начала</b></td><td><b>Время окончания</b></td><td><b>Помещение</b></td></tr>';
                            foreach ($extEvents as $extEvent) {
                                $class = 'default';
                                if (count($extEvent->checkValideTime($model->id)) > 0 || (strtotime($extEvent->lesson_end_time) - strtotime($extEvent->lesson_start_time)) / 60 < $extEvent->duration * 40 || $extEvent->lesson_date < $model->start_date || $extEvent->lesson_date > $model->finish_date) $class = 'danger';
                                echo '<tr class='.$class.'><td><h5>'.date('d.m.Y', strtotime($extEvent->lesson_date)).'</h5></td><td><h5>'.substr($extEvent->lesson_start_time, 0, -3).'</h5></td><td><h5>'.substr($extEvent->lesson_end_time, 0, -3).'</h5></td><td><h5>'.$extEvent->fullName.'</h5></td>'.
                                    '<td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-lesson', 'lessonId' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td><td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-lesson', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                            }
                            echo '</table>';
                        }
                        ?>
                    </div>
                    <div class="panel-body">
                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                            'widgetBody' => '.container-items3', // required: css class selector
                            'widgetItem' => '.item3', // required: css class
                            'limit' => 100, // the maximum times, an element can be cloned (default 999)
                            'min' => 1, // 0 or 1 (default 1)
                            'insertButton' => '.add-item3', // css class
                            'deleteButton' => '.remove-item3', // css class
                            'model' => $modelTrainingGroupAuto[0],
                            'formId' => 'dynamic-form',
                            'formFields' => [
                                'eventExternalName',
                            ],
                        ]); ?>

                        <div class="container-items3" ><!-- widgetContainer -->
                            <?php foreach ($modelTrainingGroupAuto as $i => $modelTrainingGroupAutoOne): ?>
                                <div class="item3 panel panel-default"><!-- widgetBody -->
                                    <div class="panel-heading">
                                        <h3 class="panel-title pull-left">Занятие</h3>
                                        <div class="pull-right">
                                            <button type="button" class="add-item3 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                            <button type="button" class="remove-item3 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-4">
                                            <?php
                                            $items = [1 => 'Каждый понедельник', 2 => 'Каждый вторник', 3 => 'Каждую среду', 4 => 'Каждый четверг', 5 => 'Каждую пятницу', 6 => 'Каждую субботу', 7 => 'Каждое воскресенье'];
                                            $params = [
                                                'prompt' => '',
                                                'id' => 'selectDay',
                                                'class' => 'form-control selectDayClass'
                                            ];
                                            echo $form->field($modelTrainingGroupAutoOne, "[{$i}]day")->dropDownList($items,$params)->label('Периодичность');

                                            ?>
                                        </div>
                                        <div class="col-xs-1">
                                            <?= $form->field($modelTrainingGroupAutoOne, "[{$i}]start_time")->textInput(['class' => 'form-control def', 'value' => date('h:i')])->label('Начало занятия') ?>
                                        </div>
                                        <div class="col-xs-2">
                                            <?php
                                            //$branchs = \app\models\work\BranchWork::find()->all();
                                            //$items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
                                            $params = [
                                                'id' => $i,
                                                'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('subcat') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        var elems = document.getElementsByClassName("aud1");
                                                        for (var c = 0; c !== elems.length; c++) {
                                                            if (elems[c].id == "ra" + id)
                                                                elems[c].innerHTML = res;
                                                        }
                                                    }
                                                );
                                            ',
                                            ];

                                            $audits = \app\models\work\BranchWork::find()->orderBy(['name' => SORT_ASC])->all();
                                            $items = \yii\helpers\ArrayHelper::map($audits,'id','name');

                                            echo $form->field($modelTrainingGroupAutoOne, "[{$i}]auditorium_id")->dropDownList($items,$params)->label('Отдел');

                                            ?>

                                            <?php
                                            $params = [
                                                'prompt' => '',
                                                'id' => 'ra'.$i,
                                                'class' => 'form-control aud1',
                                            ];
                                            echo $form->field($modelTrainingGroupAutoOne, "[{$i}]auds")->dropDownList([], $params)->label('Помещение'); ?>
                                        </div>


                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php DynamicFormWidget::end(); ?>
                    </div>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'open')->checkbox() ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>

    function checkSchedule()
    {

        var radioList = document.getElementsByName('scheduleType');

        if (radioList[1].checked)
        {
            document.getElementsByClassName("selectDayClass").value = null;
            $("#manualSchedule").removeAttr("hidden");
            $("#autoSchedule").attr("hidden", "true");
        }
        else
        {
            document.getElementsByClassName("inputDateClass").value = "";
            $("#autoSchedule").removeAttr("hidden");
            $("#manualSchedule").attr("hidden", "true");
        }
    }
</script>
