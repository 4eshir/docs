<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchAsAdmin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="as-admin-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'as_company_id') ?>

    <?= $form->field($model, 'document_number') ?>

    <?= $form->field($model, 'document_date') ?>

    <?= $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'country_prod_id') ?>

    <?php // echo $form->field($model, 'license_start') ?>

    <?php // echo $form->field($model, 'license_finish') ?>

    <?php // echo $form->field($model, 'version_id') ?>

    <?php // echo $form->field($model, 'license_id') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'scan') ?>

    <?php // echo $form->field($model, 'register_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
