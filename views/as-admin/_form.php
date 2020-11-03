<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\AsAdmin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="as-admin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'as_company_id')->textInput() ?>

    <?= $form->field($model, 'document_number')->textInput() ?>

    <?= $form->field($model, 'document_date')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'country_prod_id')->textInput() ?>

    <?= $form->field($model, 'license_start')->textInput() ?>

    <?= $form->field($model, 'license_finish')->textInput() ?>

    <?= $form->field($model, 'version_id')->textInput() ?>

    <?= $form->field($model, 'license_id')->textInput() ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'register_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
