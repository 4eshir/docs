<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\work\MaterialObjectWork */
/* @var $form yii\widgets\ActiveForm */
?>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<div class="material-object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'photoFile')->fileInput(['multiple' => false]) ?>

    <?= $form->field($model, 'price')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>

    <?php
    $invoices = \app\models\work\InvoiceWork::find()->orderBy(['number' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($invoices,'id','number');
    $params = [
        'style' => 'width: 60%'
    ];
    echo $form->field($model, 'number')->dropDownList($items,$params);

    ?>

    <?php
    $items = ['ОС' => 'ОС', 'ТМЦ' => 'ТМЦ'];
    $params = [
        'style' => 'width: 20%'
    ];
    echo $form->field($model, 'attribute')->dropDownList($items,$params);

    ?>

    <?php
    $finances = \app\models\work\FinanceSourceWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($finances,'id','name');
    $params = [
        'style' => 'width: 30%'
    ];
    echo $form->field($model, 'finance_source_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'inventory_number')->textInput(['maxlength' => true, 'style' => 'width: 60%']) ?>

    <?php
    $kinds = \app\models\work\KindObjectWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($kinds,'id','name');

    $params = [
        'prompt' => '--',
        'style' => 'width: 30%',
        'onchange' => '
        $.post(
            "' . Url::toRoute(['subcat', 'modelId' => $model->id]) . '", 
            {id: $(this).val()}, 
            function(res){
                let elem = document.getElementById("chars");
                elem.innerHTML = res;
            }
        );
    ',
    ];
    echo $form->field($model, 'kind_id')->dropDownList($items,$params);

    ?>

    <div id="chars">
        <?php 

        if ($model->kind_id !== null)
        {
            $characts = \app\models\work\KindCharacteristicWork::find()->where(['kind_object_id' => $model->kind_id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
            echo '<div style="border: 1px solid #D3D3D3; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; margin-bottom: 20px; border-radius: 5px; width: 35%">';
            foreach ($characts as $c)
            {
                $value = \app\models\work\ObjectCharacteristicWork::find()->where(['material_object_id' => $model->id])->andWhere(['characteristic_object_id' => $c->id])->one();
                $val = null;
                if ($value !== null)
                {
                    if ($value->integer_value !== null) $val = $value->integer_value;
                    if ($value->double_value !== null) $val = $value->double_value;
                    if (strlen($value->string_value) > 0) $val = $value->string_value;
                }

                $type = "text";
                if ($c->characteristicObjectWork->value_type == 1 || $c->characteristicObjectWork->value_type == 2) $type = "number";
                echo $form->field($model, 'characteristics[]')->textInput(['type' => $type])->label($c->characteristicObjectWork->name);
                /*echo '<div style="width: 50%; float: left; margin-top: 10px"><span>'.$c->characteristicObjectWork->name.': </span></div><div style="margin-top: 10px; margin-right: 0; min-width: 40%"><input type="'.$type.'" class="form-inline" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" name="MaterialObjectWork[characteristics][]" value="'.$val.'"></div>';*/
            }
            echo '</div>';
        }

        ?>
    </div>

    <?php
    $items = [1 => 'Нерасходуемый', 2 => 'Расходуемый'];
    $params = [
        'id' => 'type-choose',
        'style' => 'width: 20%'
    ];
    echo $form->field($model, 'type')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'is_education')->checkbox() ?>

    <div id="state-div" style="display: <?php echo $model->type == 2 ? 'block' : 'none'; ?>">
        <?= $form->field($model, 'state')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>
    </div>

    <?= $form->field($model, 'damage')->textarea(['rows' => '5']) ?>

    <?= $form->field($model, 'status')->checkbox(); ?>

    <?php
    $items = [0 => 'Списание не требуется', 1 => 'Готов к списанию', 2 => 'Списан'];
    $params = [
        'style' => 'width: 30%'
    ];
    echo $form->field($model, 'write_off')->dropDownList($items,$params);

    ?>

    <?php echo $form->field($model, 'create_date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата производства',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <?php echo $form->field($model, 'lifetime')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания эксплуатации',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <?php echo $form->field($model, 'expirationDate')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания срока годности',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    $("#type-choose").change(function(){
        var elem = document.getElementById("state-div");
        if (this.value == 2)
            elem.style.display = "block";
        else
            elem.style.display = "none";
    });
</script>