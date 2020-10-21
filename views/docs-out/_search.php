<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchDocumentOut */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'document_date') ?>

    <?= $form->field($model, 'document_theme') ?>

    <?= $form->field($model, 'destination_id') ?>

    <?= $form->field($model, 'signed_id') ?>

    <?php // echo $form->field($model, 'executor_id') ?>

    <?php // echo $form->field($model, 'send_method_id') ?>

    <?php // echo $form->field($model, 'sent_date') ?>

    <?php // echo $form->field($model, 'Scan') ?>

    <?php // echo $form->field($model, 'register_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
