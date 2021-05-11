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

    <?php
    $branchs = \app\models\common\Branch::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [
        'prompt' => '---'
    ];

    echo $form->field($model, 'eventBranchs')->dropDownList($items,$params)->label('Мероприятие проводит');

    ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
