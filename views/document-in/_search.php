<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchDocumentIn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-in-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'local_number') ?>

    <?= $form->field($model, 'local_date') ?>

    <?= $form->field($model, 'real_number') ?>

    <?= $form->field($model, 'real_date') ?>

    <?php // echo $form->field($model, 'position_id') ?>

    <?php // echo $form->field($model, 'company_id') ?>

    <?php // echo $form->field($model, 'document_theme') ?>

    <?php // echo $form->field($model, 'signed_id') ?>

    <?php // echo $form->field($model, 'target') ?>

    <?php // echo $form->field($model, 'get_id') ?>

    <?php // echo $form->field($model, 'scan') ?>

    <?php // echo $form->field($model, 'applications') ?>

    <?php // echo $form->field($model, 'register_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
