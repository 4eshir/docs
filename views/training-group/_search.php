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
    <div>
        <div style="float: left; margin-right: 10px">
            <?= $form->field($model, 'startDateSearch', ['template' => '{label}&nbsp;{input}',
                'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
                'dateFormat' => 'php:Y-m-d',
                'language' => 'ru',
                'options' => [
                    'placeholder' => '',
                    'class'=> 'form-control',
                    'style' => 'width: 100px',
                    'autocomplete'=>'off'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '2000:2050',
                ]])->label('С') ?>
        </div>
        <div style="float: left">
            <?= $form->field($model, 'finishDateSearch', [ 'template' => '{label}&nbsp;{input}',
                'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
                'dateFormat' => 'php:Y-m-d',
                'language' => 'ru',
                'options' => [
                    'placeholder' => '',
                    'class'=> 'form-control',
                    'style' => 'width: 100px',
                    'autocomplete'=>'off'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '2000:2050',
                ]])->label('По') ?>
        </div>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>
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
        <?= Html::a('Сбросить фильтры', \yii\helpers\Url::to(['training-group/index'])) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
