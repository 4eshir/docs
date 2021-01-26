<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchForeignEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="foreign-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'company_id') ?>

    <?= $form->field($model, 'start_date') ?>

    <?= $form->field($model, 'finish_date') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'event_way_id') ?>

    <?php // echo $form->field($model, 'event_level_id') ?>

    <?php // echo $form->field($model, 'min_participants_age') ?>

    <?php // echo $form->field($model, 'max_participants_age') ?>

    <?php // echo $form->field($model, 'business_trip') ?>

    <?php // echo $form->field($model, 'escort_id') ?>

    <?php // echo $form->field($model, 'order_participation_id') ?>

    <?php // echo $form->field($model, 'order_business_trip_id') ?>

    <?php // echo $form->field($model, 'key_words') ?>

    <?php // echo $form->field($model, 'docs_achievement') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
