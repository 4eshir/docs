<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\MaterialObjectWork */
/* @var $form yii\widgets\ActiveForm */
?>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<div class="material-object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'photoFile')->fileInput(['multiple' => false]) ?>

    <?= $form->field($model, 'count')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>

    <?= $form->field($model, 'price')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>

    <?= $form->field($model, 'number')->textInput(['style' => 'width: 60%']) ?>

    <?php
    $items = ['ОС' => 'ОС', 'ТМЦ' => 'ТМЦ'];
    $params = [
        'style' => 'width: 20%'
    ];
    echo $form->field($model, 'attribute')->dropDownList($items,$params);

    ?>

    <?php
    $finances = \app\models\work\FinanceSourceWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($finances,'id','name');
    $params = [
        'style' => 'width: 30%'
    ];
    echo $form->field($model, 'finance_source_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'inventory_number')->textInput(['maxlength' => true, 'style' => 'width: 60%']) ?>

    <?php
    $items = [1 => 'Нерасходуемый', 2 => 'Расходуемый'];
    $params = [
        'id' => 'type-choose',
        'style' => 'width: 20%'
    ];
    echo $form->field($model, 'type')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'is_education')->checkbox() ?>

    <div id="state-div" style="display: <?php echo $model->type == 2 ? 'block' : 'none'; ?>">
        <?= $form->field($model, 'state')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>
    </div>

    <?= $form->field($model, 'damage')->textarea(['rows' => '5']) ?>

    <?= $form->field($model, 'status')->checkbox(); ?>

    <?php
    $items = [0 => '-', 1 => 'Готов к списанию', 2 => 'Списан'];
    $params = [
        'style' => 'width: 30%'
    ];
    echo $form->field($model, 'write_off')->dropDownList($items,$params);

    ?>

    <?php echo $form->field($model, 'create_date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата производства',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <?php echo $form->field($model, 'lifetime')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания эксплуатации',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <?php echo $form->field($model, 'expirationDate')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания срока годности',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    $("#type-choose").change(function(){
        var elem = document.getElementById("state-div");
        if (this.value == 2)
            elem.style.display = "block";
        else
            elem.style.display = "none";
    });
</script>