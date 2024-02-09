<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\HistoryObject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="history-object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'material_object_id')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'container_id')->textInput() ?>

    <?= $form->field($model, 'history_transaction_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
