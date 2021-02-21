<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchTrainingProgram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-program-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'ped_council_date') ?>

    <?= $form->field($model, 'ped_council_number') ?>

    <?= $form->field($model, 'author_id') ?>

    <?php // echo $form->field($model, 'capacity') ?>

    <?php // echo $form->field($model, 'student_left_age') ?>

    <?php // echo $form->field($model, 'student_right_age') ?>

    <?php // echo $form->field($model, 'focus') ?>

    <?php // echo $form->field($model, 'allow_remote') ?>

    <?php // echo $form->field($model, 'doc_file') ?>

    <?php // echo $form->field($model, 'edit_docs') ?>

    <?php // echo $form->field($model, 'key_words') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
