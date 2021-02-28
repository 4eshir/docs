<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchTrainingGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'training_program_id') ?>

    <?= $form->field($model, 'teacher_Id') ?>

    <?= $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'finish_date') ?>

    <?php // echo $form->field($model, 'photos') ?>

    <?php // echo $form->field($model, 'present_data') ?>

    <?php // echo $form->field($model, 'work_data') ?>

    <?php // echo $form->field($model, 'open') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
