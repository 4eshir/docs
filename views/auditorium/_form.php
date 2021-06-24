<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\AuditoriumWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auditorium-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'square')->textInput() ?>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_education')->checkbox(['id' => 'org', 'onclick' => "checkEdu()"]) ?>

    <?php
    if ($model->is_education === 1)
    {
        echo '<div id="orghid">';
    }
    else
    {
        echo '<div id="orghid" hidden>';
    }
    ?>

    <?= $form->field($model, 'capacity')->textInput() ?>

    </div>

    <?php
    $branchs = \app\models\work\BranchWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [];

    echo $form->field($model, 'branch_id')->dropDownList($items,$params);
    ?>

    <?= $form->field($model, 'filesList[]')->fileInput(['multiple' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $('#org').change(function()
    {
        if (this.checked === true)
            $("#orghid").removeAttr("hidden");
        else
            $("#orghid").attr("hidden", "true");
    });
</script>