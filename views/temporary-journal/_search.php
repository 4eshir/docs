<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchTemporaryJournal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temporary-journal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'material_object_id') ?>

    <?= $form->field($model, 'give_people_id') ?>

    <?= $form->field($model, 'gain_people_id') ?>

    <?= $form->field($model, 'date_issue') ?>

    <?php // echo $form->field($model, 'approximate_time') ?>

    <?php // echo $form->field($model, 'date_delivery') ?>

    <?php // echo $form->field($model, 'branch_id') ?>

    <?php // echo $form->field($model, 'auditorium_id') ?>

    <?php // echo $form->field($model, 'event_id') ?>

    <?php // echo $form->field($model, 'foreign_event_id') ?>

    <?php // echo $form->field($model, 'signed_give') ?>

    <?php // echo $form->field($model, 'signed_gain') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'files') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
