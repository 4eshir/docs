<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchTemporaryObjectJournal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temporary-object-journal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_give_id') ?>

    <?= $form->field($model, 'user_get_id') ?>

    <?= $form->field($model, 'confirm_give') ?>

    <?= $form->field($model, 'confirm_get') ?>

    <?php // echo $form->field($model, 'material_object_id') ?>

    <?php // echo $form->field($model, 'container_id') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'date_give') ?>

    <?php // echo $form->field($model, 'date_get') ?>

    <?php // echo $form->field($model, 'real_date_get') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
