<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\TrainingGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-group-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Состав</h4></div>
            <div>
            <?php
            $extEvents = \app\models\common\TrainingGroupParticipant::find()->where(['training_group_id' => $model->id])->all();
            if ($extEvents != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td><b>ФИО</b></td><td><b>Номер сертфииката</b></td><td><b>Способ доставки</b></td></tr>';
                foreach ($extEvents  as $extEvent) {
                    echo '<tr><td><h5>'.$extEvent->participant->fullName.'</h5></td><td><h5>'.$extEvent->certificat_number.'</h5></td><td><h5>'.$extEvent->sendMethod->name.'</h5></td><td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['training-group/delete-participant', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
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
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
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
                                <h3 class="panel-title pull-left">Участник</h3>
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

                                    $people = \app\models\common\ForeignEventParticipants::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelTrainingGroupParticipantOne, "[{$i}]participant_id")->dropDownList($items,$params)->label('ФИО участника');
                                    ?>

                                </div>
                                <div class="col-xs-4">
                                    <?= $form->field($modelTrainingGroupParticipantOne, "[{$i}]certificat_number")->textInput()->label('Номер сертификата') ?>
                                </div>
                                <div class="col-xs-4">
                                    <?php
                                    $sendMethod= \app\models\common\SendMethod::find()->all();
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

    <?php
    $progs = \app\models\common\TrainingProgram::find()->all();
    $items = \yii\helpers\ArrayHelper::map($progs,'id','name');
    $params = [
    ];
    echo $form->field($model, 'training_program_id')->dropDownList($items,$params);

    ?>

    <?php
    $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'teacher_id')->dropDownList($items,$params);

    ?>

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

    <?= $form->field($model, 'open')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
