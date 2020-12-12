<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'start_date') ?>

    <?= $form->field($model, 'finish_date') ?>

    <?= $form->field($model, 'event_type_id') ?>

    <?= $form->field($model, 'event_form_id') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'event_level_id') ?>

    <?php // echo $form->field($model, 'participants_count') ?>

    <?php // echo $form->field($model, 'is_federal') ?>

    <?php // echo $form->field($model, 'responsible_id') ?>

    <?php // echo $form->field($model, 'key_words') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'order_id') ?>

    <?php // echo $form->field($model, 'regulation_id') ?>

    <?php // echo $form->field($model, 'protocol') ?>

    <?php // echo $form->field($model, 'photos') ?>

    <?php // echo $form->field($model, 'reporting_doc') ?>

    <?php // echo $form->field($model, 'other_files') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
