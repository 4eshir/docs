<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\ContractWork */
/* @var $form yii\widgets\ActiveForm */
?>

<script type="text/javascript">
    window.onload = function(){
        let elem = document.getElementById('all_category');
        let ids = elem.innerHTML.split(' ');

        let checks = document.getElementsByClassName('cat');

        for (let i = 0; i < ids.length; i++)
            for (let j = 0; j < checks.length; j++)
                if (ids[i] == checks[j].value)
                    checks[j].setAttribute('checked', 'checked');
    }
</script>

<style type="text/css">
    .checkList{
        border: 1px solid #dddddd;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .checkBlock{
        height: 200px;
        overflow-y: scroll;
        margin-right: -15px;
        margin-bottom: -15px;
        margin-top: -15px;

        padding-top: 10px;
    }

    .checkHeader{
        background: #f5f5f5;
        border-bottom: 1px solid #dddddd;
        margin-top: -15px;
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: 15px;
        line-height: 2em;
    }

    .noPM{
        margin: 0;
        padding: 0;
        line-height: 3;
        padding-left: 15px;
    }
</style>

<div id="all_category" style="display: none;"><?php
    $cat = \app\models\work\ContractCategoryContractWork::find()->where(['contract_id' => $model->id])->all();
    $res = '';
    foreach ($cat as $one)
        $res .= $one->category_contract_id.' ';
    $res = substr($res, 0, -1);
    echo $res;
    ?>
</div>

<div class="contract-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'style' => 'width: 20%',
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]])->label('Дата договора') ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true])->label('Номер договора') ?>

    <?php
    $coms = \app\models\work\CompanyWork::find()->where(['is_contractor' => 1])->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($coms,'id','name');
    $params = [];
    echo $form->field($model, 'contractor_id')->dropDownList($items,$params)->label('Контрагент');

    ?>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Категории материальных объектов в договоре</h4>
        </div>

        <div class="checkBlock">
            <?php
            $category = \app\models\work\CategoryContractWork::find()->orderBy(['name' => SORT_ASC])->all();
            $items = \yii\helpers\ArrayHelper::map($category,'id','name');

            echo $form->field($model, 'category')->checkboxList($items, [
                'item' => function($index, $label, $name, $checked, $value) {
                    return "<div 'class'='col-sm-12'><label><input class='cat' type='checkbox' {$checked} name='{$name}'value='{$value}'> {$label}</label></div>";
                }])->label(false) ?>
        </div>

    </div>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>

    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан договора') ?>
    <?php
    if (strlen($model->file) > 2)
        echo '<h5>Загруженный файл: '.Html::a($model->file, \yii\helpers\Url::to(['contract/get-file', 'fileName' => $model->file, 'modelId' => $model->id])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['contract/delete-file', 'fileName' => $model->file, 'modelId' => $model->id])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
