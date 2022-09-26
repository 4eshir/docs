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

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'photo_local') ?>

    <?= $form->field($model, 'photo_cloud') ?>

    <?= $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'attribute') ?>

    <?php // echo $form->field($model, 'finance_source_id') ?>

    <?php // echo $form->field($model, 'inventory_number') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'is_education') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'damage') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'write_off') ?>

    <?php // echo $form->field($model, 'lifetime') ?>

    <?php // echo $form->field($model, 'expiration_date') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
