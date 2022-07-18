<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ForeignEventReportModel */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php
$this->title = 'Отчет по гос. заданию';
?>

<style>
    .block-report{
        background: #e9e9e9;
        width: auto;
        padding: 10px 10px 0 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        margin-right: 10px;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Введите период для генерации отчета</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'start_date', ['template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'id' => 'date1',
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

    <div class="col-xs-6 block-report">
        <?= $form->field($model, 'end_date', [ 'template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'id' => 'date2',
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
    <div class="col-xs-8 block-report" id="hours">
            <?php
            $arr = ['0' => 'Метод, учитывающий неявки', '1' => 'Метод, игнорирующий неявки'];
            echo $form->field($model, 'method')->radioList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="methods-'. $index .'">
                            <input style="margin-left: -20px" id="methods-'. $index .'" name="'. $name .'" type="radio" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
            }])->label('Метод подсчета человеко-часов');
            ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="form-group">
        <?= Html::submitButton('Скачать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    var elem = document.getElementById('date1');

    elem.onchange = function()
    {
        var Dy = new Date().toLocaleDateString();
        console.log(Dy);
        var elem1 = document.getElementById('date2');
        var dateArr = Dy.split('.');
        elem1.value = dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0];
    }
</script>