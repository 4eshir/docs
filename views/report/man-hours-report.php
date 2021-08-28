<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ManHoursReportModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="man-hours-report-form">

    <h5><b>Введите период для генерации отчета</b></h5>
    <div class="col-xs-6" style="padding-left: 0; width: auto">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'start_date', ['template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('С') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'end_date', [ 'template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('По') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="col-xs-8" style="padding-left: 0">
        <?= $form->field($model, 'type')->checkboxList(['0' => 'Кол-ву человеко-часов', '1' => 'Кол-ву уникальных обучающихся, завершивших обучение в заданный период', '2' => 'Кол-ву всех обучающихся, завершивших обучение в заданный период'],
            [
                'item' => function($index, $label, $name, $checked, $value)
                {
                    return '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black">
                                <label for="interview-'. $index .'">
                                    <input id="interview-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                    <span></span>
                                    '. $label .'
                                </label>
                            </div>';
                }
            ])->label('Сгенерировать отчет по'); ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="form-group">
        <?= Html::submitButton('Генерировать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
