<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ForeignEventReportModel */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php
//--
$this->title = 'Нагрузка преподавателей';
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

    <h5><b>Выберите год и отдел</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

        <?php
        $currentYear = date('Y');
        $yearArr = [];
        $yearArr[] = $currentYear - 2;
        $yearArr[] = $currentYear - 1;
        $yearArr[] = $currentYear;
        $yearArr[] = $currentYear + 1;
        $items = [];
        foreach ($yearArr as $ye)
            $items += [$ye => $ye];
        $params = [
        ];
        echo $form->field($model, 'year', ['template' => '{label}&nbsp;{input}', 'options' => ['class' => 'form-group form-inline']])->dropDownList($items,$params)->label('Год');
        ?>

    </div>

    <div class="col-xs-6 block-report">
        <?php
        $branch = \app\models\work\BranchWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
        $params = [
        ];
        echo $form->field($model, 'branch', ['template' => '{label}&nbsp;{input}', 'options' => ['class' => 'form-group form-inline']])->dropDownList($items,$params)->label('Отдел');
        ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

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
        var Dy = new Date(elem.value);
        Dy.setFullYear(Dy.getFullYear() + 1);
        var elem1 = document.getElementById('date2');
        elem1.value = Dy.getFullYear() + "-" + ('0' + (Dy.getMonth() + 1)).slice(-2) + "-" + ('0' + Dy.getDate()).slice(-2);
    }
</script>