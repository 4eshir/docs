<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'document_date')->textInput() ?>

    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_id')->textInput() ?>

    <?= $form->field($model, 'signed_id')->textInput() ?>

    <?= $form->field($model, 'executor_id')->textInput() ?>

    <?= $form->field($model, 'send_method_id')->textInput() ?>

    <?= $form->field($model, 'sent_date')->textInput() ?>

    <?= $form->field($model, 'Scan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'register_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
