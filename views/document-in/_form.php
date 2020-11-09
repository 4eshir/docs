<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentIn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-in-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'local_number')->textInput()->label('Локальный номер') ?>

    <?= $form->field($model, 'local_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'оставить поле пустым, если бессрочно',
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
        ]])->label('Локальная дата') ?>

    <?= $form->field($model, 'real_number')->textInput()->label('Номер исходящего документа') ?>

    <?= $form->field($model, 'real_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'оставить поле пустым, если бессрочно',
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
        ]])->label('Дата исходящего документа') ?>

    <?php
    $position = \app\models\common\Position::find()->all();
    $items = \yii\helpers\ArrayHelper::map($position,'id','name');
    $params = [

    ];
    echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');

    ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [];
    echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');

    ?>

    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true])->label('Тема документа') ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as value", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'signedString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кем подписан');

    ?>

    <?= $form->field($model, 'target')->textInput(['maxlength' => true])->label('Кому адресован') ?>

    <?php
    $sendMethod= \app\models\common\SendMethod::find()->all();
    $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
    $params = [];
    echo $form->field($model, 'send_method_id')->dropDownList($items,$params)->label('Способ отправки');

    ?>

    <?php
    $people = \app\models\common\User::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as value", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'getString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кем принят');

    ?>

    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан документа')?>

    <?= $form->field($model, 'applicationFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label('Приложения') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
