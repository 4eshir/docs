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
    $params = [
        'prompt' => '--',
    ];
    echo $form->field($model, 'branchId')->dropDownList($items, $params)->label('Отдел');
    ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => '--',
    ];
    echo $form->field($model, 'teacherId')->dropDownList($items, $params)->label('Педагог');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Очистить', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
