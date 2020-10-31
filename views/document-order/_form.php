<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-order-form">

    <?php
    $model->people_arr = \app\models\common\People::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'order_number')->textInput()->label('Номер документа') ?>

    <?= $form->field($model, 'order_name')->textInput(['maxlength' => true])->label('Название приказа') ?>

    <?= $form->field($model, 'order_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата приказа') ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as label", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'signedString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кем подписан'); ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as label", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'bringString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Проект вносит'); ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as label", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'executorString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кто исполнил'); ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ответственные</h4></div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelResponsible[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                    <?php foreach ($modelResponsible as $i => $modelResponsibleOne): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Ответственный</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelResponsibleOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelResponsibleOne, "[{$i}]id");
                                }
                                ?>

                                <?php
                                echo $form->field($modelResponsibleOne, "[{$i}]fio")->widget(
                                    \yii\jui\AutoComplete::className(), [
                                    'clientOptions' => [
                                        'source' => $people,
                                    ],
                                    'options'=>[
                                        'class'=>'form-control',
                                    ]
                                ])->label('ФИО');

                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'scanFile')->fileInput() ?>

    <?php
    $people = \app\models\common\People::find()->select(["CONCAT(secondname, ' ', firstname, ' ', patronymic) as label", "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $params = [];
    echo $form->field($model, 'registerString')->widget(
        \yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => $people,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ])->label('Кто регистрировал'); ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить приказ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
