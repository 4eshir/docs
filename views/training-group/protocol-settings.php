<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ProtocolForm */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php
$this->title = 'Протокол итоговой аттестации';
?>

<style>

</style>

<div class="man-hours-report-form">

    <h5><b>Выберите название публичного мероприятия или введите его вручную</b></h5>

    <?php $form = ActiveForm::begin(); ?>

    <?php /*echo $form->field($model, 'dropdownEventName')->
        dropDownList(['Научно-техническая конференция SchoolTech Conference' => 'Научно-техническая конференция SchoolTech Conference',
                    'Демонстрация результатов образовательной деятельности' => 'Демонстрация результатов образовательной деятельности'])->label(false)*/ ?>
    <?php echo $form->field($model, 'textEventName')->textInput(['value' => 'Научно-техническая конференция SchoolTech Conference',
        'placeholder' => 'Демонстрация результатов образовательной деятельности'])->label(false) ?>

    <br>
    <label><b>Выделите всех присутствовавших на защите:</b></label><br>
    <div class="checkbox-list">
        <?= $form->field($model, 'chooseParticipants')->checkboxList(
            \yii\helpers\ArrayHelper::map($model->participants, 'id', function ($participant) {
                return $participant->participant['secondname'] . ' ' . $participant->participant['firstname'] . ' ' . $participant->participant['patronymic'];
            }),
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return Html::checkbox($name, $checked, [
                        'value' => $value,
                        'label' => $label,
                        'checked' => true,
                    ]);
                },
            ]
        )->label(false) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Скачать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .checkbox-list label {
        display: block;
    }
</style>