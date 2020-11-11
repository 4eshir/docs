<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\People */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="people-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'secondname')->textInput(['maxlength' => true])->label('Фамилия') ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true])->label('Имя') ?>

    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => true])->label('Отчество') ?>

    <?php
    $position = \app\models\common\Position::find()->all();
    $items = \yii\helpers\ArrayHelper::map($position,'id','name');
    $params = [];
    echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность');

    ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [];
    echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация');

    ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
