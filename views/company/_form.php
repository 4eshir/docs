<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\CompanyWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inn')->textInput()->label('ИНН организации'); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название организации') ?>

    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true])->label('Краткое название организации') ?>

    <?php
    $company_type = \app\models\work\CompanyTypeWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company_type,'id','type');
    $params = [];
    echo $form->field($model, 'company_type_id')->dropDownList($items,$params)->label('Тип организации');

    ?>

    <?= $form->field($model, 'is_contractor')->checkbox(['onchange' => 'ContractorChange(this)']); ?>

    <?php
    $dis = $model->is_contractor == 1 ? 'block' : 'none';
    ?>



    <div id="contractor" style="display: <?php echo $dis; ?>">


        <?php
        $smsp = \app\models\work\CategorySmspWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($smsp,'id','name');
        $params = [
            'prompt' => 'НЕ СМСП',
        ];
        echo $form->field($model, 'category_smsp_id')->dropDownList($items,$params)->label('Категория СМСП');

        ?>

        <?php
        $types = \app\models\work\OwnershipTypeWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($types,'id','name');
        $params = [
            'prompt' => '--',
        ];
        echo $form->field($model, 'ownership_type_id')->dropDownList($items,$params)->label('Форма собственности');

        ?>

        <?= $form->field($model, 'okved')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'head_fio')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'site')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'comment')->textarea(['rows' => '3']) ?>   
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function ContractorChange(e)
    {
        let elem = document.getElementById('contractor');
        if (e.checked)
            elem.style.display = "block";
        else
            elem.style.display = "none";

    }
</script>