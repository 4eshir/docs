<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Auditorium */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auditorium-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'square')->textInput() ?>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_education')->checkbox() ?>

    <?php
    $branchs = \app\models\common\Branch::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [];

    echo $form->field($model, 'branch_id')->dropDownList($items,$params);
    ?>

    <?= $form->field($model, 'filesList[]')->fileInput(['multiple' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
