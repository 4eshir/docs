<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата начала мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата начала мероприятия') ?>

    <?= $form->field($model, 'finish_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата окончания мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата окончания мероприятия') ?>

    <?php
    $orders = \app\models\common\EventType::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'event_type_id')->dropDownList($items,$params)->label('Тип мероприятия');

    ?>

    <?php
    $orders = \app\models\common\EventForm::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'event_form_id')->dropDownList($items,$params)->label('Форма мероприятия');

    ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\common\EventLevel::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'event_level_id')->dropDownList($items,$params)->label('Уровень мероприятия');

    ?>

    <?= $form->field($model, 'participants_count')->textInput() ?>

    <?= $form->field($model, 'is_federal')->checkbox() ?>

    <?php
    $orders = \app\models\common\People::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','shortName');
    $params = [];

    echo $form->field($model, 'responsible_id')->dropDownList($items,$params)->label('Ответственный за мероприятие');

    ?>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\common\DocumentOrder::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [];

    echo $form->field($model, 'order_id')->dropDownList($items,$params)->label('Приказ по мероприятию');

    ?>

    <?php
    $orders = \app\models\common\Regulation::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'regulation_id')->dropDownList($items,$params)->label('Положение по мероприятию');

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Внешние мероприятия</h4></div>
            <?php
            $extEvents = \app\models\common\EventsLink::find()->where(['event_id' => $model->id])->all();
            if ($extEvents != null)
            {
                echo '<table>';
                foreach ($extEvents  as $extEvent) {
                    echo '<tr><td style="padding-left: 20px"><h4>"'.$extEvent->eventExternal->name.'"</h4></td> <td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['event/delete-external-event', 'id' => $extEvents->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
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
                    'model' => $modelEventsLinks[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items1" ><!-- widgetContainer -->
                    <?php foreach ($modelEventsLinks as $i => $modelEventsLink): ?>
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Мероприятие</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelEventsLink->isNewRecord) {
                                    echo Html::activeHiddenInput($modelEventsLink, "[{$i}]id");
                                }
                                ?>
                                <div>
                                    <?php

                                    $branch = \app\models\common\Branch::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                    $params = [];
                                    echo $form->field($modelEventsLink, "[{$i}]eventExternalName")->textInput()->label('Название мероприятия');
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


    <?= $form->field($model, 'protocolFile')->fileInput() ?>

    <?= $form->field($model, 'photoFiles[]')->fileInput(['multiple' => true]) ?>

    <?= $form->field($model, 'reportingFile')->fileInput() ?>

    <?= $form->field($model, 'otherFiles[]')->fileInput(['multiple' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
