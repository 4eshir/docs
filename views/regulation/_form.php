<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\RegulationWork */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$session = Yii::$app->session;
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
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\work\DocumentOrderWork::find()->where(['!=', 'order_name', 'Резерв'])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [];

    echo $form->field($model, "order_id")->dropDownList($items,$params)->label('Приказ');

    ?>

    <?= $form->field($model, 'ped_council_number')->textInput(['type' => $session->get('type') == 1 ? 'text' : 'hidden'])->label($session->get('type') == 1 ? null : false) ?>

    <?= $form->field($model, 'ped_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата совета',
            'class'=> 'form-control',
            'autocomplete'=>'off',
            'type' => $session->get('type') == 1 ? 'text' : 'hidden'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label($session->get('type') == 1 ? 'Дата педагогического совета' : false) ?>

    <?= $form->field($model, 'par_council_number')->textInput(['type' => $session->get('type') == 1 ? 'text' : 'hidden'])->label($session->get('type') == 1 ? null : false) ?>

    <?= $form->field($model, 'par_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата собрания',
            'class'=> 'form-control',
            'autocomplete'=>'off',
            'type' => $session->get('type') == 1 ? 'text' : 'hidden'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label($session->get('type') == 1 ? 'Дата совета родителей' : false) ?>



    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан положения')?>

    <?php
    if (strlen($model->scan) > 2)
        echo '<h5>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['regulation/get-file', 'fileName' => $model->scan])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['regulation/delete-file', 'fileName' => $model->scan, 'modelId' => $model->id, 'type' => 'scan'])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

