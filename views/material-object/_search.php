<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchMaterialObject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="material-object-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'unique_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'acceptance_date') ?>

    <?= $form->field($model, 'balance_price') ?>

    <?php // echo $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'main') ?>

    <?php // echo $form->field($model, 'files') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
