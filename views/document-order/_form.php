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

    <?= $form->field($model, 'order_number')->textInput()->label('Преамбула') ?>

    <?= $form->field($model, 'order_name')->textInput(['maxlength' => true])->label('Название приказа') ?>

    <?php
    $people = \app\models\common\People::find()->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'bring_id')->dropDownList($items,$params)->label('Проект вносит');

    ?>

    <?php
    $people = \app\models\common\People::find()->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'executor_id')->dropDownList($items,$params)->label('Кто исполнил');

    ?>
    <br>
    <?php
    echo $form->field($model, 'allResp')
    ->checkbox([
        'label' => 'Добавить всех работников в ответственных',
        'labelOptions' => [
        ],
    ]);
    ?>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ответственные</h4></div>
            <?php
            $resp = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
            if ($resp != null)
            {
                echo '<table>';
                foreach ($resp as $respOne) {
                    $respOnePeople = \app\models\common\People::find()->where(['id' => $respOne->people_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$respOnePeople->secondname.' '.$respOnePeople->firstname.' '.$respOnePeople->patronymic.'</h4></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['document-order/delete-responsible', 'peopleId' => $respOnePeople->id, 'orderId' => $model->id])).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
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
                                $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'fullName','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelResponsibleOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО');

                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан приказа') ?>
    <?php
    if ($model->scan !== null)
        echo '<h5>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $model->scan])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить приказ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
