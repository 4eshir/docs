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
    $position = \app\models\common\Position::find()->all();
    $items = \yii\helpers\ArrayHelper::map($position,'id','name');
    $params = [
        
    ];
    echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');

    ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [];
    echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');

    ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as value", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'signedString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кем подписан');

    ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as value", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'executorString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кто исполнил');

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
