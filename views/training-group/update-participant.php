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

    <?php
    $sendMethod= \app\models\work\SendMethodWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
    $params = [
        'prompt' => ''
    ];
    echo $form->field($model, 'send_method_id')->dropDownList($items,$params)->label('Способ доставки');

    ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
