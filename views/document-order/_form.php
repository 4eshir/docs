<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_number')->textInput()->label('Номер документа') ?>

    <?= $form->field($model, 'order_name')->textInput(['maxlength' => true])->label('Название приказа') ?>

    <?= $form->field($model, 'order_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]])->label('Дата приказа') ?>

    <?php
    $people = \app\models\common\People::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'signed_id')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кем подписан'); ?>

    <?php
    $people = \app\models\common\People::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'bring_id')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Проект вносит'); ?>

    <?php
    $people = \app\models\common\People::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'executor_id')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кто исполнил'); ?>

    <?= $form->field($model, 'scanFile')->fileInput() ?>

    <?php
    $people = \app\models\common\People::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'register_id')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кто регистрировал'); ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить приказ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
