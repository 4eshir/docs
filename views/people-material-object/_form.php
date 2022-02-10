<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\PeopleMaterialObjectWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="people-material-object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'people_id')->dropDownList($items,$params);

    ?>

    <?php
    $objects = \app\models\work\MaterialObjectWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($objects,'id','name');
    $params = [
        'disabled'=> $model->material_object_id !== null ? 'disabled' : null,
    ];
    echo $form->field($model, 'material_object_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'acceptance_date')->widget(\yii\jui\DatePicker::class, [
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
        ]])?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
