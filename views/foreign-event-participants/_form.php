<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventParticipantsWork */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'secondname')->textInput() ?>

    <?= $form->field($model, 'firstname')->textInput() ?>

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
    <div>
        <?= $form->field($model, 'sex')->radioList(array('Мужской' => 'Мужской',
            'Женский' => 'Женский', 'Другое' => 'Другое'), ['value' => $model->sex, 'class' => 'i-checks']) ?>
    </div>

    <div <?php echo $model->is_true === 1 || $model->guaranted_true === 1 ? 'hidden' : ''; ?>>
        <?php
        $value = $model->guaranted_true === 1 ? true : false;
        ?>

        <?= $form->field($model, 'guaranted_true')->checkbox(['checked' => $value]) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
