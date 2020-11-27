<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="regulation-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::class, [
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
        ]])->label('Дата положения') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\common\DocumentOrder::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [];

    echo $form->field($model, "order_id")->dropDownList($items,$params)->label('Приказ');

    ?>

    <?= $form->field($model, 'ped_council_number')->textInput() ?>

    <?= $form->field($model, 'ped_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата совета',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата педагогического совета') ?>

    <?= $form->field($model, 'par_council_number')->textInput() ?>

    <?= $form->field($model, 'par_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата собрания',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата родительского собрания') ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Утратили силу приказы</h4></div>
            <br>
            <?php
            $order = \app\models\common\Expire::find()->where(['active_regulation_id' => $model->order_id])->all();
            if ($order != null)
            {
                echo '<table>';
                foreach ($order as $orderOne) {
                    echo '<tr><td style="padding-left: 20px"><h4><b>Утратил силу приказ: </b>'.$orderOne->expireRegulation->fullName.'</h4></td><td style="padding-left: 10px">'
                        .Html::a('Отменить', \yii\helpers\Url::to(['regulation/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Вы уверены?',
                                'method' => 'post',
                            ],]).'</td></tr>';
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
                    'model' => $modelExpire[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                    <?php foreach ($modelExpire as $i => $modelExpireOne): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Приказ</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelExpireOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelExpireOne, "[{$i}]id");
                                }
                                ?>
                                <?php
                                $orders = \app\models\common\DocumentOrder::find()->all();
                                $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
                                $params = [
                                    'prompt' => '',
                                ];

                                echo $form->field($modelExpireOne, "[{$i}]expire_regulation_id")->dropDownList($items,$params)->label('Приказ');

                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан положения')?>

    <?php
    if ($model->scan !== null)
        echo '<h5>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['regulation/get-file', 'fileName' => $model->scan])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

