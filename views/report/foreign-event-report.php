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

    .block-age{
        background: #e9e9e9;
        width: 95px;
        padding: 10px 10px 0 5px;
        margin-bottom: 20px;
        margin-right: 0;
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
        echo $form->field($model, 'branch')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
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
    <div class="col-xs-8 block-report">
        <?php
        $focus = \app\models\work\FocusWork::find()->all();
        $arr = \yii\helpers\ArrayHelper::map($focus, 'id', 'name');
        echo $form->field($model, 'focus')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="focus-'. $index .'">
                        <input id="focus-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Направленность');
        ?>
    </div>
    <div class="col-xs-8 block-report">
        <p><b>Возраст</b></p>
        <div class="col-xs-10 block-age">
            <?php
            echo $form->field($model, 'age_left', ['template' => "<div><div style='float: left; margin-right: 5px; margin-top: 5px'>{label}</div><div style='float: left; width: 50px; padding-bottom: 10px'>{input}</div></div>"])->textInput()->label('C');
            ?>
        </div>
        <div class="col-xs-10 block-age">
            <?php
            echo $form->field($model, 'age_right', ['template' => "<div><div style='float: left; margin-right: 5px; margin-top: 5px'>{label}</div><div style='float: left; width: 50px; padding-bottom: 10px'>{input}</div></div>"])->textInput()->label('По');
            ?>
        </div>
    </div>
    <div class="col-xs-8 block-report">
        <?php
        $branchs = \app\models\work\BranchWork::find()->all();
        $arr = ['Мужской', 'Женский'];
        echo $form->field($model, 'sex')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="sex-'. $index .'">
                        <input id="sex-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Пол');
        ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="form-group">
        <?= Html::submitButton('Генерировать отчет', ['class' => 'btn btn-primary']) ?>
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