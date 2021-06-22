<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryJournal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temporary-journal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'material_object_id')->textInput() ?>

    <?= $form->field($model, 'give_people_id')->textInput() ?>

    <?= $form->field($model, 'gain_people_id')->textInput() ?>

    <?= $form->field($model, 'date_issue')->textInput() ?>

    <?= $form->field($model, 'approximate_time')->textInput() ?>

    <?= $form->field($model, 'date_delivery')->textInput() ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'auditorium_id')->textInput() ?>

    <?= $form->field($model, 'event_id')->textInput() ?>

    <?= $form->field($model, 'foreign_event_id')->textInput() ?>

    <?= $form->field($model, 'signed_give')->textInput() ?>

    <?= $form->field($model, 'signed_gain')->textInput() ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'files')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
