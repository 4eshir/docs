<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

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


    <?= $form->field($model, 'protocolFile')->fileInput() ?>

    <?= $form->field($model, 'photos')->fileInput(['multiple' => true]) ?>

    <?= $form->field($model, 'reporting_doc')->fileInput() ?>

    <?= $form->field($model, 'other_files')->fileInput(['multiple' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
