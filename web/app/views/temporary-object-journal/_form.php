<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryObjectJournal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temporary-object-journal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_give_id')->textInput() ?>

    <?= $form->field($model, 'user_get_id')->textInput() ?>

    <?= $form->field($model, 'confirm_give')->textInput() ?>

    <?= $form->field($model, 'confirm_get')->textInput() ?>

    <?= $form->field($model, 'material_object_id')->textInput() ?>

    <?= $form->field($model, 'container_id')->textInput() ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_give')->textInput() ?>

    <?= $form->field($model, 'date_get')->textInput() ?>

    <?= $form->field($model, 'real_date_get')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
