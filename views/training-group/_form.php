<?php

use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\jui\AutoComplete;
use app\models\components\RoleBaseAccess;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupWork */
/* @var $form yii\widgets\ActiveForm */
?>

<style>

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

    html, body {
        min-height: 100%;
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

    div.image-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 100%;
        bottom: 0;
        background-color: #fff;
        z-index: 999999;
        text-align: center;
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

    .training-btn {
        margin-right: 10px;
    }
</style>

<script>
    let counter = 0;

    function allCheck(main)
    {
        var elems = document.getElementsByClassName('check');
        var count1 = document.getElementById("lesCount1");
        var count2 = document.getElementById("lesCount2");
        var c1 = 0;
        for (var c = 0; c !== elems.length; c++)
        {
            if (main.checked) elems[c].checked = true;
            else elems[c].checked = false;
        }
        var str = main.checked ? elems.length / 2 : 0;
        count1.innerHTML = '<i>Выделено занятий:</i> ' + str;
        count2.innerHTML = '<i>Выделено занятий:</i> ' + str;
    }

    function oneCheck(elem)
    {
        var count1 = document.getElementById("lesCount1");
        var count2 = document.getElementById("lesCount2");
        var currentC = parseInt(count1.innerHTML.split(" ").slice(-1));
        if (elem.checked) currentC++;
        else currentC--;
        count1.innerHTML = '<i>Выделено занятий:</i> ' + currentC;
        count2.innerHTML = '<i>Выделено занятий:</i> ' + currentC;
    }


</script>

<?php
$js =<<< JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        var d = new Date();
        var elems = document.getElementsByClassName('def');
        elems[elems.length - 1].value = '10:00';
    });
    $(".dynamicform_wrapper1").on("afterInsert", function(e, item) {
        counter = counter + 1;
        var elems1 = document.getElementsByClassName('part');
        elems1[elems1.length - 1].id = 'participant_id' + counter;
    });
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
?>

<?php
$session = Yii::$app->session;
?>

<?php
$roles = [5, 6, 7];
$isMethodist = \app\models\work\UserRoleWork::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'role_id', $roles])->one();
?>

<div class="training-group-form">

    <?php
        echo Html::button('Показать общую информацию', ['class' => 'btn btn-primary training-btn', 'onclick' => 'switchBlock("common")']);
        if (!($model->branch_id === null || $model->budget === null || $model->training_program_id === null || $model->start_date === null || $model->finish_date === null))
        {
            echo Html::button('Показать список учеников', ['class' => 'btn btn-primary training-btn', 'onclick' => 'switchBlock("parts")']);
            echo Html::button('Показать расписание', ['class' => 'btn btn-primary training-btn', 'onclick' => 'switchBlock("schedule")']);
            echo Html::button('Показать сведения о защите работ', ['class' => 'btn btn-primary training-btn', 'onclick' => 'switchBlock("protection")']);
        }
    ?>
    <div style="height: 20px"></div>
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div id="common">

        <?php
        $branch = \app\models\work\BranchWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
        $params = [
        ];
        echo $form->field($model, 'branch_id')->dropDownList($items,$params);
        ?>

        <?= $form->field($model, 'budget')->checkbox() ?>



    <?php
    $counterPhp = 0;

    if ($model->training_program_id !== null)
        $progs = \app\models\work\TrainingProgramWork::find()->orderBy(['name' => SORT_ASC])->andWhere(['actual' => 1])->orWhere(['id' => $model->training_program_id])->all();
    else
        $progs = \app\models\work\TrainingProgramWork::find()->orderBy(['name' => SORT_ASC])->andWhere(['actual' => 1])->all();

    $items = \yii\helpers\ArrayHelper::map($progs,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'training_program_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'is_network')->checkbox() ?>

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
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
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


    <div <?php echo $isMethodist === null ? 'hidden' : null ?>>
        <?= //$form->field($model, 'order_stop')->checkbox()->label(false);
        $form->field($model, 'order_stop')->checkbox() ?>
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
                        echo '<tr><td><h5>'.$order->documentOrderWork->fullName.'</h5></td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-order', 'id' => $order->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger md-trigger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <?php /*
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

            </div>*/?>
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

    <div id="parts" hidden>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Состав</h4></div>
                <?php
                    if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 10) || (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 11)))
                    {
                        echo '<div style="padding-left: 1.5%; padding-top: 1%">';
                        echo $form->field($model, 'fileParticipants')->fileInput();
                        echo '</div>';
                        echo '<div style="padding-left: 1.5%; padding-top: 1%">';
                        echo $form->field($model, 'certFile')->fileInput();
                        echo '</div>';
                    }
                ?>

                <div>
                    <?php
                    $extEvents = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->id])->orderBy(['participant.secondname' => SORT_ASC, 'participant.firstname' => SORT_ASC, 'participant.patronymic' => SORT_ASC])->all();
                    if ($extEvents != null)
                    {
                        echo '<div style="overflow-y: scroll; max-height: 300px;"><table class="table table-bordered">';
                        echo '<tr><td><b>ФИО</b></td><td><b>Номер сертификата</b></td><td><b>Способ доставки</b></td></tr>';
                        $c = 0;
                        foreach ($extEvents  as $extEvent) {
                            if ($extEvent->status == 0) {
                                $sm = \app\models\work\SendMethodWork::find()->all();
                                $items = \yii\helpers\ArrayHelper::map($sm, 'id', 'name');
                                $params = [
                                    'prompt' => '--',
                                    'value' => $model->sendMethodArr[$c],
                                ];
                                //echo '<tr><td><h5>'.$extEvent->participantWork->fullName.'</h5></td><td><h5>'.$extEvent->certificat_number.'</h5></td><td><h5>'.$extEvent->sendMethod->name.'</h5></td><td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-participant', 'id' => $extEvent->id]), ['class' => 'btn btn-primary']).'</td>'.
                                echo '<tr><td><h5>' . $extEvent->participantWork->fullName . '</h5></td><td><h5>';
                                        /*$form->field($model, 'certificatArr[]')->textInput(['value' => $model->certificatArr[$c]])->label(false) .*/
                                if (!empty($model->certificatArr[$c]))
                                    echo $model->certificatArr[$c];     // поддержка старых сертификатов
                                else
                                    echo $extEvent->certificatWork->CertificatLongNumber;   // новый вид сертификатов
                                echo $form->field($model, 'idArr[]')->hiddenInput(['value' => $extEvent->id])->label(false). '</h5></td><td><h5>' .
                                        $form->field($model, 'sendMethodArr[]')->dropDownList($items, $params)->label(false) . '</h5></td><td>&nbsp;' .
                                        Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-participant', 'id' => $extEvent->id]), ['class' => 'btn btn-primary']) . '</td>' .
                                    //'<td>&nbsp;' . Html::a('Отчислить', \yii\helpers\Url::to(['training-group/remand-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-warning']) . '</td>' .
                                    '<td>&nbsp;' . Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger md-trigger']) . '</td></tr>';
                            }else
                                echo '<tr style="background: lightcoral"><td><h5>'.$extEvent->participantWork->fullName.'</h5></td><td><h5>'.
                                    $extEvent->certificat_number.'</h5></td><td><h5>'.
                                    $extEvent->sendMethod->name.'</h5></td><td>&nbsp;'.
                                    Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-participant', 'id' => $extEvent->id]), ['class' => 'btn btn-primary']).'</td>'.
                                    //'<td>&nbsp;'.Html::a('Восстановить', \yii\helpers\Url::to(['training-group/unremand-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-success']).'</td>'.
                                    '<td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger md-trigger']).'</td></tr>';
                            $c++;
                        }
                        echo '</table></div>';
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
                        'insertButton' => '.add-item1', // css class
                        'deleteButton' => '.remove-item1', // css class
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
                                        <button type="button" class="add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                        <button type="button" class="remove-item1 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
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
                                    <div class="col-xs-6">

                                        <?php

                                        $people = \app\models\work\ForeignEventParticipantsWork::find()->select(['CONCAT(secondname, \' \', firstname, \' \', patronymic) as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic, ' ', birthdate) as label", 'id as id'])->where(['is_true' => 1])->orWhere(['guaranted_true' => 1])->asArray()->all();

                                        echo $form->field($modelTrainingGroupParticipantOne, "[{$i}]participant_name")->widget(
                                            AutoComplete::className(), [
                                            'clientOptions' => [
                                                'source' => $people,
                                                'select' => new JsExpression("function( event, ui ) {
                                                    $('#participant_id' + counter).val(ui.item.id); //#memberssearch-family_name_id is the id of hiddenInput.
                                                 }"),
                                            ],
                                            'options'=>[
                                                'class'=>'form-control on',
                                            ]
                                        ])->label('ФИО учащегося');

                                        ?>

                                        <input class="part" type="hidden" id="participant_id0" name="TrainingGroupParticipantWork[<?php echo $i; ?>][participant_id]">


                                    </div>
                                    <!--<div class="col-xs-4">
                                        <?php /*$form->field($modelTrainingGroupParticipantOne, "[{$i}]certificat_number")->textInput()->label('Номер сертификата')*/ ?>
                                    </div>-->
                                    <div class="col-xs-4">
                                        <?php
                                        $sendMethod= \app\models\work\SendMethodWork::find()->orderBy(['name' => SORT_ASC])->all();
                                        $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
                                        $params = [
                                            'prompt' => ''
                                        ];
                                        echo $form->field($modelTrainingGroupParticipantOne, "[{$i}]send_method_id")->dropDownList($items,$params)->label('Способ доставки сертификата');

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

    <div id="schedule" hidden>

        <?= $form->field($model, 'schedule_type')->radioList(array('0' => 'Ручное заполнение расписания',
            '1' => 'Автоматическое расписание по дням'), ['value' => '0', 'name' => 'scheduleType', 'onchange' => 'checkSchedule()'])->label('') ?>

        <div id="manualSchedule">
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ручное заполнение расписания</h4></div>
                    <div>
                        <?php
                            $extEvents = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->id])->orderBy(['lesson_date' => SORT_ASC, 'lesson_start_time' => SORT_ASC])->all();

                        if ($extEvents != null)
                        {
                            echo '<div style="overflow-y: scroll; max-height: 300px"><table class="table table-bordered">';
                            echo '<tr><td><input type="checkbox" id="checker0" onclick="allCheck(this)"></td><td><b>Дата</b></td><td><b>Время начала</b></td><td><b>Время окончания</b></td><td><b>Помещение</b></td></tr>';
                            $counter = 0;
                            foreach ($extEvents as $extEvent) {
                                $class = 'default';
                                echo '<tr class='.$class.'>'.
                                    '<td>'.$form->field($model, 'delArr[]')->checkbox(['id' => 'traininggroupwork-delarr'.$counter, 'value' => $extEvent->id, 'class' => 'check', 'onclick' => "oneCheck(this)"], false)->label(false).'</td>'.'<td><h5>'.date('d.m.Y', strtotime($extEvent->lesson_date)).'</h5></td><td><h5>'.substr($extEvent->lesson_start_time, 0, -3).'</h5></td><td><h5>'.substr($extEvent->lesson_end_time, 0, -3).'</h5></td><td><h5>'.$extEvent->fullName.'</h5></td>'.
                                    '<td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-lesson', 'lessonId' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td><td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-lesson', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger md-trigger']).'</td></tr>';
                                $counter++;
                            }
                            echo '</table></div>';
                        }
                        ?>
                    </div>
                    <?php
                    if (count($extEvents) > 0)
                    {
                        echo '<div class="form-group" style="padding-left: 15px; margin-bottom: 50px">';
                                echo '<div style="float:left">'.Html::submitButton('Удалить выделенные', ['class' => 'btn btn-danger md-trigger', 'name' => 'deleteChoose']).'</div>';
                        echo '<div style="float:left; margin-left: 15px; line-height: 2.5" id="lesCount1"><i>Выделено занятий:</i> 0</div>';
                        echo '</div><hr style="border: 0.5px solid gray">';
                    }
                    ?>

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
                                        <div class="col-xs-2">
                                            <?= $form->field($modelTrainingGroupLessonOne, "[{$i}]lesson_start_time")->textInput(['type' => 'time', 'class' => 'form-control def', 'value' => '08:00', 'min'=>'08:00', 'max'=>'20:00'])->label('Начало занятия') ?>

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
                        $extEvents = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->id])->orderBy(['lesson_date' => SORT_ASC, 'lesson_start_time' => SORT_ASC])->all();

                        if ($extEvents != null)
                        {
                            echo '<div style="overflow-y: scroll; max-height: 300px"><table class="table table-bordered">';
                            echo '<tr><td><input type="checkbox" id="checker1" onclick="allCheck(this)"></td><td><b>Дата</b></td><td><b>Время начала</b></td><td><b>Время окончания</b></td><td><b>Помещение</b></td></tr>';
                            $counter = 0;
                            foreach ($extEvents as $extEvent) {
                                $class = 'default';
                                echo '<tr class='.$class.'>'.
                                    '<td>'.$form->field($model, 'delArr[]')->checkbox(['id' => 'traininggroupwork-delarr'.$counter, 'value' => $extEvent->id, 'class' => 'check', 'onclick' => "oneCheck(this)"], false)->label(false).'</td>'.'<td><h5>'.date('d.m.Y', strtotime($extEvent->lesson_date)).'</h5></td><td><h5>'.substr($extEvent->lesson_start_time, 0, -3).'</h5></td><td><h5>'.substr($extEvent->lesson_end_time, 0, -3).'</h5></td><td><h5>'.$extEvent->fullName.'</h5></td>'.
                                    '<td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['training-group/update-lesson', 'lessonId' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td><td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-lesson', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger md-trigger', 'onclick' => 'clickSubmit()']).'</td></tr>';
                                $counter++;
                            }
                            echo '</table></div>';
                        }
                        ?>
                    </div>
                    <?php
                    if (count($extEvents) > 0)
                    {
                        echo '<div class="form-group" style="padding-left: 15px; margin-bottom: 50px">';
                                echo '<div style="float:left">'.Html::submitButton('Удалить выделенные', ['class' => 'btn btn-danger md-trigger', 'name' => 'deleteChoose']).'</div>';
                        echo '<div style="float:left; margin-left: 15px; line-height: 2.5" id="lesCount2"><i>Выделено занятий:</i> 0</div>';
                        echo '</div><hr style="border: 0.5px solid gray">';

                        //echo '<div class="form-group" style="padding-left: 15px; padding-top: 10px">';
                        //echo Html::submitButton('Удалить выделенные', ['class' => 'btn btn-danger md-trigger', 'name' => 'deleteChoose']);
                        //echo '</div>';

                    }
                    ?>
                    <div class="panel-body">
                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_wrapper3', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
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
                                            echo $form->field($modelTrainingGroupAutoOne, "[{$i}]day")->checkboxList(
                                                    ['0' => 'Каждый понедельник', '1' => 'Каждый вторник', '2' => 'Каждую среду', '3' => 'Каждый четверг', '4' => 'Каждую пятницу', '5' => 'Каждую субботу', '6' => 'Каждое воскресенье'],
                                                    ['item'=>function ($index, $label, $name, $checked, $value){
                                                        if($checked)
                                                            $checked = 'checked';
                                                        return '<label class="checkbox-inline">
                                                                <input class="'.$index.'" type="checkbox" value="' . $value . '" name="' . $name . '" ' . $checked . ' />'.$label.'
                                                                </label><br>';
                                            }])->label('<div style="padding-bottom: 10px">Периодичность</div>');
                                            /*$items = [1 => 'Каждый понедельник', 2 => 'Каждый вторник', 3 => 'Каждую среду', 4 => 'Каждый четверг', 5 => 'Каждую пятницу', 6 => 'Каждую субботу', 7 => 'Каждое воскресенье'];
                                            $params = [
                                                'prompt' => '',
                                                'id' => 'selectDay',
                                                'class' => 'form-control selectDayClass'
                                            ];
                                            echo $form->field($modelTrainingGroupAutoOne, "[{$i}]day")->dropDownList($items,$params)->label('Периодичность');
                                            */
                                            ?>
                                        </div>
                                        <div class="col-xs-2">
                                            <?= $form->field($modelTrainingGroupAutoOne, "[{$i}]start_time")->textInput(['type' => 'time','class' => 'form-control def', 'value' => '08:00', 'min'=>'08:00', 'max'=>'20:00'])->label('Начало занятия') ?>

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

        <?php echo $form->field($model, 'open')->checkbox() ?>
    </div>

    <div id="protection" hidden>

        <div>
            <?php echo $form->field($model, 'protection_date')->widget(\yii\jui\DatePicker::class,
                [
                    'dateFormat' => 'php:Y-m-d',
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Дата защиты',
                        'class'=> 'form-control',
                        'autocomplete'=>'off',
                    ],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '2000:2050',
                    ]]) 
            ?>
        </div>

        <div class="row"  style="display: <?php echo $model->trainingProgramWork->certificat_type_id == 1 || $model->trainingProgramWork->certificat_type_id == 4 ? 'block' : 'none' ?>;">
            <div class="panel panel-default">
                <div class="panel-heading"><p style="width: 77.5%; text-align: left; font-family: Tahoma; font-size: 20px; padding-left: 0">Темы проектов</p></div>
                <?php
                $themes = \app\models\work\GroupProjectThemesWork::find()->joinWith(['projectTheme projectTheme'])->where(['training_group_id' => $model->id])->all();
                if ($themes != null)
                {
                    echo '<table>';
                    foreach ($themes as $theme) {
                        $confirmButton = '';
                        $strConfirm = '';
                        if ($theme->confirm == 0)
                        {
                            $confirmButton .= Html::a('Утвердить', \yii\helpers\Url::to(['training-group/confirm-theme', 'id' => $theme->id, 'modelId' => $model->id]), ['class' => 'btn btn-success', 'style' => 'margin-right: 20px']);
                            $strConfirm .= '<span style="font-size: 12pt; color: red; margin-left: 10px; margin-right: 10px; padding: 0">Не утверждена</span>';
                        }
                        else
                        {
                            $confirmButton .= Html::a('Отклонить', \yii\helpers\Url::to(['training-group/decline-theme', 'id' => $theme->id, 'modelId' => $model->id]), ['class' => 'btn btn-warning', 'style' => 'margin-right: 20px']);
                            $strConfirm .= '<span style="font-size: 12pt; color: green; margin-left: 10px; margin-right: 10px; padding: 0">Утверждена</span>';
                        }

                        $role = \app\models\work\UserRoleWork::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['IN', 'role_id', [6, 7]])->one();
                        if ($role == null)
                            $confirmButton = '';

                        echo '<tr><td style="padding-left: 20px; text-align: left; padding-right: 15px"><h4>Тема: '.$theme->projectTheme->name.' </td><td><h5>('.$theme->projectType->name.' проект)</h5>'.'</h4></td><td>'.$strConfirm.'</td><td>'.$confirmButton.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-theme', 'id' => $theme->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                        if (empty($theme->projectTheme->description))
                            echo '<tr><td colspan="4" style="padding-left: 20px; text-align: left; padding-right: 15px; padding-bottom: 10px">Описание: <i style="color: red">отсутствует</i></td></tr>';
                        else
                            echo '<tr><td colspan="4" style="padding-left: 20px; text-align: left; padding-right: 15px; padding-bottom: 10px">Описание: '.$theme->projectTheme->description.'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper8', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items8', // required: css class selector
                        'widgetItem' => '.item8', // required: css class
                        'limit' => 15, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item8', // css class
                        'deleteButton' => '.remove-item8', // css class
                        'model' => $modelProjectThemes[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'eventExternalName',
                        ],
                    ]); ?>

                    <div class="container-items8" ><!-- widgetContainer -->
                        <?php foreach ($modelProjectThemes as $i => $modelProjectTheme): ?>
                            <div class="item8 panel panel-default"><!-- widgetBody -->
                                <div class="panel-heading">
                                    <div class="pull-right">
                                        <button type="button" class="add-item8 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                        <button type="button" class="remove-item8 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <div style="float:left; width: 60%">
                                        <?php

                                        $branch = \app\models\work\EventExternalWork::find()->all();
                                        $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                        $params = [
                                            'prompt' => '',
                                        ];
                                        echo $form->field($modelProjectTheme, "[{$i}]themeName")->textInput($items,$params)->label('Тема проекта');
                                        ?>

                                    </div>

                                    <div style="float:left; margin-left: 20px; width: 30%">
                                        <?php
                                        $people = \app\models\work\ProjectTypeWork::find()->all();
                                        $items = \yii\helpers\ArrayHelper::map($people,'id','name');
                                        $params = [
                                            'prompt' => ''
                                        ];
                                        echo $form->field($modelProjectTheme, "[{$i}]project_type_id")->dropDownList($items,$params)->label('Тип проекта');

                                        ?>
                                    </div>

                                    <div>
                                        <?php
                                        $branch = \app\models\work\EventExternalWork::find()->all();
                                        $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                        $params = [
                                            'prompt' => '',
                                        ];
                                        echo $form->field($modelProjectTheme, "[{$i}]themeDescription")->textInput($items,$params)->label('Краткое описание проекта');
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

        

    <div class="row" style="display: <?php echo $model->trainingProgramWork->certificat_type_id == 1 ? 'block' : 'none' ?>;">
            <div class="panel panel-default">
                <div class="panel-heading"><p style="width: 77.5%; text-align: left; font-family: Tahoma; font-size: 20px; padding-left: 0">Приглашенные эксперты</p></div>
                <?php
                $experts = \app\models\work\TrainingGroupExpertWork::find()->where(['training_group_id' => $model->id])->all();
                if ($experts != null)
                {
                    echo '<table>';
                    foreach ($experts as $expert) {
                        $color = $expert->expertType->name == 'Внутренний' ? '#f0ad4e' : 'green';
                        $expertTyprStr = '<span style="font-size: 12pt; color: red; margin-left: 10px; margin-right: 10px; padding: 0; color: '.$color.'"">'.$expert->expertType->name.'</span>';
                        echo '<tr><td style="padding-left: 20px; width: 25%"><h4>'.$expert->expert->secondname.' '.$expert->expert->firstname.' '.$expert->expert->patronymic.'</h4></td><td style="padding-left: 20px; width: 25%"><h4>'.$expert->expert->company->name.'</h4></td><td style="padding-left: 20px; width: 25%"><h4>'.$expert->expert->position->name.'</td><td style="padding-left: 20px; width: 15%">'.$expertTyprStr.'</td><td style="padding-left: 20px; width: 10%">'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-expert', 'id' => $expert->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper9', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items9', // required: css class selector
                        'widgetItem' => '.item9', // required: css class
                        'limit' => 10, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item9', // css class
                        'deleteButton' => '.remove-item9', // css class
                        'model' => $modelExperts[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'eventExternalName',
                        ],
                    ]); ?>

                    <div class="container-items9" ><!-- widgetContainer -->
                        <?php foreach ($modelExperts as $i => $modelExpert): ?>
                            <div class="item9 panel panel-default"><!-- widgetBody -->
                                <div class="panel-heading">
                                    <div class="pull-right">
                                        <button type="button" class="add-item9 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                        <button type="button" class="remove-item9 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <div style="float:left">
                                        <?php
                                        $people = \app\models\work\PeopleWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                        $params = [
                                            'prompt' => ''
                                        ];
                                        echo $form->field($modelExpert, "[{$i}]expert_id")->dropDownList($items,$params)->label('ФИО эксперта');

                                        ?>

                                        
                                    </div>

                                    <div style="float:left; margin-left: 20px; margin-top: 30px">
                                        <?= $form->field($modelExpert, "[{$i}]expert_type_id")->checkbox() ?>
                                    </div>       

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php DynamicFormWidget::end(); ?>

                </div>

            </div>
        </div>

        <div style="display: <?php echo $model->protection_date !== null || $model->trainingProgramWork->certificat_type_id != 1 ? 'block' : 'none' ?>;">
            <?php 
            if (RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 5) || RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6) || RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7))
                echo $form->field($model, 'protection_confirm')->checkbox(); ?>
        </div>
    </div>



    </div>

    

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success md-trigger', 'data-modal' => 'modal-12']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="md-modal md-effect-12">
        <div class="content-blocker">
            <div style="border-radius: 10px; margin-bottom: 200px; font-size: 24px; background: whitesmoke; padding: 5px 5px 5px 5px; margin-left: 5%;">
                Пожалуйста, подождите. Данные обновляются...
            </div>
            <div class="image-holder">
            <img src="load.gif"/>
            </div>
            <div style="border-radius: 10px; font-size: 24px; background: whitesmoke; padding: 5px 5px 5px 5px; margin-bottom: 2%;">
                Если данное окно зависло: проверьте корректность заполненных полей и повторите попытку сохранения.
                <button class="btn btn-flat md-close">Закрыть окно</button>
            </div>
        </div>
    </div>
</div>


<script>

    function switchBlock(idBlock) {
        document.querySelector('#common').hidden = true;
        document.querySelector('#parts').hidden = true;
        document.querySelector('#schedule').hidden = true;
        document.querySelector('#protection').hidden = true;
        block = '#' + idBlock;
        document.querySelector(block).hidden = false;
    }

    function checkSchedule()
    {

        var elems = document.getElementsByClassName('check');
        for (var c = 0; c !== elems.length; c++)
        {
            elems[c].checked = false;
        }

        var checker = document.getElementById('checker0');
        if (checker !== null)
            checker.checked = false;
        checker = document.getElementById('checker1');
        if (checker !== null)
            checker.checked = false;

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


        var count1 = document.getElementById("lesCount1");
        var count2 = document.getElementById("lesCount2");
        count1.innerHTML = '<i>Выделено занятий:</i> ' + 0;
        count2.innerHTML = '<i>Выделено занятий:</i> ' + 0;
    }

</script>

<?php

$people = \app\models\work\ForeignEventParticipantsWork::find()->select(['CONCAT(secondname, \' \', firstname, \' \', patronymic) as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic, ' ', birthdate) as label", 'id as id'])->where(['is_true' => 1])->orWhere(['guaranted_true' => 1])->asArray()->all();

$children = json_encode($people);

$js =<<< JS
    $(".dynamicform_wrapper1").on("click", ".on", function(e) {
        console.log($children);
      if ( !$(this).data("autocomplete") ) {
          e.preventDefault();
          $(this).autocomplete({
            source: $children,
            select: function( event, ui ) {
                $('#participant_id' + counter).val(ui.item.id);
            }
          });
      }
    })
JS;

$js1 =<<< JS
    $(".dynamicform_wrapper3").on("afterInsert", function(e, item) {
      var elems = document.getElementsByClassName("0");
      
      for (var i = 0; i < elems.length; i++)
      {
          
          elems[i].value = 0;
      }
      elems = document.getElementsByClassName("1");
      for (var i = 0; i < elems.length; i++)
      {
          elems[i].value = 1;
      }
      elems = document.getElementsByClassName("2");
      for (var i = 0; i < elems.length; i++)
      {
          elems[i].value = 2;
      }
      elems = document.getElementsByClassName("3");
      for (var i = 0; i < elems.length; i++)
      {
          elems[i].value = 3;
      }
      elems = document.getElementsByClassName("4");
      for (var i = 0; i < elems.length; i++)
      {
          elems[i].value = 4;
      }
      elems = document.getElementsByClassName("5");
      for (var i = 0; i < elems.length; i++)
      {
          elems[i].value = 5;
      }
      elems = document.getElementsByClassName("6");
      for (var i = 0; i < elems.length; i++)
      {
          elems[i].value = 6;
      }
    })
JS;

$js2 =<<< JS
    $(".md-trigger").on('click', function() {
        $(".md-modal").addClass('md-show');
    });

    $(".md-close").on('click', function() {
        $(".md-modal").removeClass("md-show");
    })
JS;


$this->registerJs($js, \yii\web\View::POS_LOAD);
$this->registerJs($js1, \yii\web\View::POS_LOAD);
$this->registerJs($js2, \yii\web\View::POS_LOAD);
?>
