<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchTrainingGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php
    $branch = \app\models\work\BranchWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
    $params = [];
    echo $form->field($model, 'branchId')->dropDownList($items, $params)->label('Отдел');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
