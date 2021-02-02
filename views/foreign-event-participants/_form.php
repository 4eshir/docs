<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEventParticipants */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'firstname')->textInput() ?>

    <?= $form->field($model, 'secondname')->textInput() ?>

    <?= $form->field($model, 'patronymic')->textInput() ?>

    <?= $form->field($model, 'birthdate')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '1980:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
