<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupParticipantWork */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php

    $people = \app\models\work\ForeignEventParticipantsWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => '',
    ];
    echo $form->field($model, 'participant_id')->dropDownList($items,$params)->label('ФИО участника');
    ?>

    <?= $form->field($model, 'certificat_number')->textInput()->label('Номер сертификата') ?>

    <?php
    $sendMethod= \app\models\work\SendMethodWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
    $params = [
        'prompt' => ''
    ];
    echo $form->field($model, 'send_method_id')->dropDownList($items,$params)->label('Способ доставки');

    ?>

    <?php
    $data = \app\models\work\PersonalDataWork::find()->all();
    $arr = \yii\helpers\ArrayHelper::map($data, 'id', 'name');
    echo $form->field($model, 'pd')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
        if ($checked == 1) $checked = 'checked';
        return
            '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="branch-'. $index .'">
                        <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
    }])->label('Запретить разглашение персональных данных:');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
