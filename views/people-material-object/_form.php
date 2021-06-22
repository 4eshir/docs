<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\PeopleMaterialObject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="people-material-object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'people_id')->textInput() ?>

    <?= $form->field($model, 'material_object_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
