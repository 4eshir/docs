<?php

use yii\jui\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'document_number')->label('№ документа'); ?>

    <?= $form->field($model, 'document_name')->label('Название документа'); ?>

    <?= $form->field($model, 'document_date')->widget(DatePicker::class, [
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
        ]])->label('Дата документа') ?>

    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true])->label('Тема документа') ?>
    <?php
    $position = \app\models\common\Position::find()->all();
    $items = \yii\helpers\ArrayHelper::map($position    ,'id','name');
    $params = [
        'prompt' => '---'
    ];
    echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');

    ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [];
    echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');

    ?>

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
    ])->label('Кем подписан');

    ?>

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
    ])->label('Кто исполнил');

    ?>

    <?php
    $sendMethod= \app\models\common\SendMethod::find()->all();
    $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
    $params = [];
    echo $form->field($model, 'send_method_id')->dropDownList($items,$params)->label('Способ отправки');

    ?>

    <?= $form->field($model, 'sent_date')->widget(DatePicker::class, [
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
        ]])->label('Дата отправки') ?>

    <?= $form->field($model, 'file')->fileInput() ?>

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
    ])->label('Кто регистрировал');

    ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить документ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
