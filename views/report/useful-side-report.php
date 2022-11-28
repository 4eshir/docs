<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ManHoursReportModel */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php
$this->title = 'Генерация отчета по обучающимся';
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
    <div class="col-xs-8 block-report">
        <?php
        $branchs = \app\models\work\BranchWork::find()->all();
        $arr = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
        echo $form->field($model, 'branch')->radioList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="branch-'. $index .'">
                        <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Отдел');
        ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="form-group">
        <?= Html::submitButton('Показать все группы с незаполненным УТП', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    function showHours()
    {
        var elem = document.getElementById('interview-0');
        var hour = document.getElementById('hours');
        var teach = document.getElementById('teachers');
        if (elem.checked) { hour.style.display = "block"; teach.style.display = "block"; }
        else { hour.style.display = "none"; teach.style.display = "none"; }
    }
</script>