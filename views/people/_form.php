<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use app\models\common\Position;

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

    $positionList = Position::find()->select(['name as value', 'name as label'])->asArray()->all();
    echo $form->field($model, 'stringPosition')->widget(AutoComplete::className(), [
                                            'clientOptions' => [
                                                'source' => $positionList,
                                            ],
                                            'options' => [
                                                'class' => 'form-control',
                                            ]
                                        ])->label('Должность');
    //$position = \app\models\common\Position::find()->all();
    //$items = \yii\helpers\ArrayHelper::map($position,'id','name');
    //$params = [];
    //echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность');

    ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [
        'id' => 'org'
    ];
    echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация');

    ?>

    <?php
    if ($model->company_id == 8)
    {
        echo '<div id="orghid">';
    }
    else
    {
        echo '<div id="orghid" hidden>';
    }
    ?>
        <?= $form->field($model, 'short')->textInput(['maxlength' => true]) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $("#org").change(function() {
        if (this.options[this.selectedIndex].value === '8')
            $("#orghid").removeAttr("hidden");
        else
            $("#orghid").attr("hidden", "true");
    });
</script>