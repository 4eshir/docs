<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */
/* @var $form yii\widgets\ActiveForm */
?>

<script>
    let is_data_changed = true;
    window.onbeforeunload = function () {
        return (is_data_changed ? "Измененные данные не сохранены. Закрыть страницу?" : null);
    }

    function clickSubmit()
    {
        is_data_changed = false;
    }

</script>

<div class="local-responsibility-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $rt = \app\models\work\ResponsibilityTypeWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($rt,'id','name');
    $params = [
        'disabled'=> $model->branch_id !== null ? 'disabled' : null,
    ];
    echo $form->field($model, 'responsibility_type_id')->dropDownList($items,$params);

    ?>

    <?php
    $branchs = \app\models\work\BranchWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [
        'disabled'=> $model->branch_id !== null ? 'disabled' : null,
        'prompt' => '--',
        'onchange' => '
            $.post(
                "' . Url::toRoute('subcat') . '", 
                {id: $(this).val()}, 
                function(res){
                    var elem = document.getElementsByClassName("aud");
                    elem[0].innerHTML = res;
                }
            );
        ',
    ];
    echo $form->field($model, 'branch_id')->dropDownList($items,$params);

    ?>

    <?php
    //$auds = \app\models\work\AuditoriumWork::find()->all();
    //$items = \yii\helpers\ArrayHelper::map($auds,'id','name');
    $params = [
        'disabled'=> $model->branch_id !== null ? 'disabled' : null,
        'class' => 'form-control aud',
    ];
    if ($model->branch_id === null)
        echo $form->field($model, 'auditorium_id')->dropDownList([],$params);
    else
    {
        $auds = \app\models\work\AuditoriumWork::find()->where(['branch_id' => $model->branch_id])->all();
        $items = \yii\helpers\ArrayHelper::map($auds,'id','name');
        $params = [
            'disabled'=> $model->branch_id !== null ? 'disabled' : null,
            'class' => 'form-control aud',
            'prompt' => '--',
        ];
        echo $form->field($model, 'auditorium_id')->dropDownList($items,$params);
    }

    ?>

    <?= $form->field($model, 'quant')->input('text', ['placeholder'=>"Введите целое число, если необходима дополнительная идентификация ответственности", 'readonly' => 'true']); ?>

    <?php
    if ($model->people_id == null)
    {
        $peoples = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
        $items = \yii\helpers\ArrayHelper::map($peoples,'id','fullName');
        $params = [
            'prompt' => '--'
        ];
        echo $form->field($model, 'people_id')->dropDownList($items,$params);
        echo $form->field($model, 'start_date')->widget(DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            //'dateFormat' => 'dd.MM.yyyy,
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1980:2050',
                //'showOn' => 'button',
                //'buttonText' => 'Выбрать дату',
                //'buttonImageOnly' => true,
                //'buttonImage' => 'images/calendar.gif'
            ]])->label('Дата прикрепления ответственности');

        $orders = \app\models\work\DocumentOrderWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [
            'prompt' => '',
        ];
        echo $form->field($model, 'order_id')->dropDownList($items,$params)->label('Приказ');
    }
    else
    {
        echo '<table class="table table-bordered">'.
            '<tr><td><b>Ответственное лицо</b></td><td><b>Дата открепления ответственности</b></td><td><b>Приказ</b></td></tr>';
        echo '<tr><td>'.\app\models\work\PeopleWork::find()->where(['id' => $model->people_id])->one()->shortName.'</td><td>';
        echo $form->field($model, 'end_date')->widget(DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            //'dateFormat' => 'dd.MM.yyyy,
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1980:2050',
                //'showOn' => 'button',
                //'buttonText' => 'Выбрать дату',
                //'buttonImageOnly' => true,
                //'buttonImage' => 'images/calendar.gif'
            ]])->label(false);
        echo '</td><td>'.Html::a(\app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one()->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $model->order_id])). '</td>';
        echo '</td><td>'.Html::submitButton('Открепить', ['class' => 'btn btn-danger', 'onclick' => 'clickSubmit()']). '</td><tr>';
        echo '</table>';
        $orders = \app\models\work\DocumentOrderWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [
            'prompt' => '',
        ];
        echo $form->field($model, 'order_id')->dropDownList($items,$params)->label('Приказ');
    }



    ?>

    <?php
    $regs = \app\models\work\RegulationWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($regs,'id','name');
    $params = [
        'prompt' => '',
    ];
    echo $form->field($model, 'regulation_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'filesStr[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->files) > 2)
    {
        $split = explode(" ", $model->files);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['local-responsibility/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['local-responsibility/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'onclick' => 'clickSubmit()']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
