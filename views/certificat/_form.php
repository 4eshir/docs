<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Certificat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="certificat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'certificat_number')->textInput() ?>

    <?= $form->field($model, 'certificat_template_id')->textInput() ?>

    <?= $form->field($model, 'training_group_participant_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
