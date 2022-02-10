<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\RoleWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="role-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Наименование роли') ?>

    <?php
    $data = \app\models\work\RoleFunctionWork::find()->all();
    $arr = \yii\helpers\ArrayHelper::map($data, 'id', 'name');
    echo $form->field($model, 'functions')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
        if ($checked == 1) $checked = 'checked';
        return
            '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: #000000;">
                    <label for="branch-' . $index .'">
                        <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
    }])->label('Права доступа');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
