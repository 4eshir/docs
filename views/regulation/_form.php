<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="regulation-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата положения') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\common\DocumentOrder::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [];
    echo $form->field($model, "order_id")->dropDownList($items,$params)->label('Приказ');

    ?>

    <?= $form->field($model, 'ped_council_number')->textInput() ?>

    <?= $form->field($model, 'ped_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата совета',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата педагогического совета') ?>

    <?= $form->field($model, 'par_council_number')->textInput() ?>

    <?= $form->field($model, 'par_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата собрания',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата родительского собрания') ?>

    <?php
    $status = ['Актуально', 'Утратило силу'];
    $params = [
        'id' => 'corr',
    ];
    echo $form->field($model, "order_id")->dropDownList($status,$params)->label('Состояние');

    ?>

    <?php
    if ($model->state !== 'Утратило силу')
    {
        echo '<div id="corr_div1" hidden="true">';

        $orders = \app\models\common\DocumentOrder::find()->all();
        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [];
        echo $form->field($model, "expireOrder")->dropDownList($items,$params)->label('В соответствии с приказом');

        echo '</div>';
    }
    else
    {
        echo '<div id="corr_div1">';

        $orders = \app\models\common\DocumentOrder::find()->all();
        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [];
        echo $form->field($model, "expireOrder")->dropDownList($items,$params)->label('В соответствии с приказом');

        echo '</div>';
    }
    ?>

    <?= $form->field($model, 'scan')->fileInput()
        ->label('Скан положения')?>

    <?php
    if ($model->scan !== null)
        echo '<h5>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['regulation/get-file', 'fileName' => $model->scan])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $("#corr").change(function() {
        if (this.value != 1) {
            $("#corr_div1").attr("hidden", "true");
        }
        else
        {
            $("#corr_div1").removeAttr("hidden");
        }
    });
</script>