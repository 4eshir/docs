<?php

use yii\jui\DatePicker;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'document_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]])->label('Дата документа') ?>

    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true])->label('Тема документа') ?>

    <?php
    $people = \app\models\common\People::find()->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => 'Выберите корреспондента',
        'id' => 'corr',
    ];
    echo $form->field($model, 'correspondent_id')->dropDownList($items,$params)->label('ФИО корреспондента');

    ?>
    <?php 
        if ($model->correspondent_id !== null)
        {
            echo '<div id="corr_div1" hidden="true">';
                $position = \app\models\common\Position::find()->all();
                $items = \yii\helpers\ArrayHelper::map($position,'id','name');
                $params = [
                    'id' => 'position',
                ];
                echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');
            echo '</div>';

            echo '<div id="corr_div2" hidden="true">';
                $company = \app\models\common\Company::find()->all();
                $items = \yii\helpers\ArrayHelper::map($company,'id','name');
                $params = [
                    'id' => 'company',
                ];
                echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');
            echo '</div>';
        }
        else
        {
            echo '<div id="corr_div1">';
                $position = \app\models\common\Position::find()->all();
                $items = \yii\helpers\ArrayHelper::map($position,'id','name');
                $params = [
                    'id' => 'position',
                ];
                echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');
            echo '</div>';

            echo '<div id="corr_div2">';
                $company = \app\models\common\Company::find()->all();
                $items = \yii\helpers\ArrayHelper::map($company,'id','name');
                $params = [
                    'id' => 'company',
                ];
                echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');
            echo '</div>';
        }
    ?>
    

    <?php
    $people = \app\models\common\People::find()->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'signed_id')->dropDownList($items,$params)->label('Кем подписан');

    ?>

    <?php
    $people = \app\models\common\People::find()->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'executor_id')->dropDownList($items,$params)->label('Кто исполнил');

    ?>

    <?php
    $sendMethod= \app\models\common\SendMethod::find()->all();
    $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
    $params = [];
    echo $form->field($model, 'send_method_id')->dropDownList($items,$params)->label('Способ отправки');

    ?>

    <?= $form->field($model, 'sent_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]])->label('Дата отправки') ?>

    <div class="padding-v-md">
        <div class="line line-dashed"></div>
    </div>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>

    <?= $form->field($model, 'scanFile')->fileInput(['initialPreview' => $model->imagesLinks, 'initialPreviewAsData' => true, 'overwriteInitial' => false])
        ->label('Скан документа')?>

    <?php
        if ($model->Scan !== null)
            echo '<h5>Загруженный файл: '.Html::a($model->Scan, \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $model->Scan])).'</h5><br>';
    ?>

    <?= $form->field($model, 'applicationFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label('Приложения') ?>

    <?php
    if ($model->applications !== null)
    {
        $split = explode(" ", $model->applications);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл : '.Html::a($split[$i], \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['docs-out/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <br>
        <?= Html::submitButton('Добавить документ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $("#corr").change(function() {
        if (this.value != '') {
            $("#corr_div1").attr("hidden", "true");
            $("#corr_div2").attr("hidden", "true");
        }
        else
        {
            $("#corr_div1").removeAttr("hidden");
            $("#corr_div2").removeAttr("hidden");
        }
    });
</script>