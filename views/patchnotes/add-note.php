<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use app\models\work\PatchnotesWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\PatchnotesWork */
/* @var $form yii\widgets\ActiveForm */
?>

<h2>Добавить патчноут</h2>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'date')->widget(DatePicker::class, [
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
        'yearRange' => '1940:2050',
        //'showOn' => 'button',
        //'buttonText' => 'Выбрать дату',
        //'buttonImageOnly' => true,
        //'buttonImage' => 'images/calendar.gif'
    ]])->label('Дата патчноута') ?>

<?= $form->field($model, 'text')->textarea(['rows' => '10'])->label('Список изменений через Enter') ?>

<?= $form->field($model, 'checker')->checkbox() ?>

<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
