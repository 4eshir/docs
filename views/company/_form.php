<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $company_type = \app\models\common\CompanyType::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company_type,'id','type');
    $params = [];
    echo $form->field($model, 'company_type_id')->dropDownList($items,$params)->label('Тип организации');

    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название организации') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
