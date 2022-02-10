<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\MaterialObjectWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="material-object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'unique_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $types = \app\models\work\MaterialObjectTypeWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($types,'id','name');
    $params = [];
    $params0 = [
    ];
    echo $form->field($model, 'event_level_id')->dropDownList($items,  $params);

    ?>

    <?= $form->field($model, 'acceptance_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => '',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])?>


    <?= $form->field($model, 'balance_price')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'main')->checkbox() ?>

    <?= $form->field($model, 'upFiles')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->files) > 2)
    {
        $split = explode(" ", $model->files);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['material-object/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['material-object/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
