<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupLessonWork */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lesson_date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата проведения занятия',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('Дата занятия') ?>

    <div class="col-xs-2" style="padding-left: 0;">
        <?= $form->field($model, 'lesson_start_time')->textInput(['type' => 'time', 'class' => 'form-control def', 'min'=>'08:00', 'max'=>'20:00'])->label('Начало занятия') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <?php
    $audits = \app\models\work\BranchWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($audits,'id','name');
    $params = [
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

    echo $form->field($model, 'branch_id')->dropDownList($items,$params)->label('Отдел');

    $params = [
        'prompt' => 'Вне отдела',
        'class' => 'form-control aud',
    ];

    if ($model->branch_id === null)
        echo $form->field($model, 'auditorium_id')->dropDownList([], $params)->label('Помещение');
    else
    {
        $auds = \app\models\work\AuditoriumWork::find()->where(['branch_id' => $model->branch_id])->all();
        $items = \yii\helpers\ArrayHelper::map($auds,'id','fullName');
        echo $form->field($model, 'auditorium_id')->dropDownList($items, $params)->label('Помещение');
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
