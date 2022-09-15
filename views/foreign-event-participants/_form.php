<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventParticipantsWork */
/* @var $form yii\bootstrap\ActiveForm */
?>

<script src="/scripts/sisyphus/sisyphus.js"></script>
<script src="/scripts/sisyphus/sisyphus.min.js"></script>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'secondname')->textInput() ?>

    <?= $form->field($model, 'firstname')->textInput() ?>

    <?= $form->field($model, 'patronymic')->textInput() ?>

    <?= $form->field($model, 'birthdate')->widget(DatePicker::class, [
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
        ]]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <div>
        <?= $form->field($model, 'sex')->radioList(array('Мужской' => 'Мужской',
            'Женский' => 'Женский', 'Другое' => 'Другое'), ['value' => $model->sex, 'class' => 'i-checks']) ?>
    </div>

    <?php
    if ($model->id === null || $model->is_true === 1 || $model->guaranted_true === 1)
        echo '<div hidden>'; //echo $model->is_true === 1 || $model->guaranted_true === 1 ? 'hidden' : '';
    else
        echo '<div>';
        $value = $model->guaranted_true === 1 ? true : false;
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6) || \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7))
            echo $form->field($model, 'guaranted_true')->checkbox(['checked' => $value]) ;
    echo '</div>';
    ?>


    <?php
    $data = \app\models\work\PersonalDataWork::find()->all();
    $arr = \yii\helpers\ArrayHelper::map($data, 'id', 'name');
    if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 22) )
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

<script type="text/javascript">
    $('form').sisyphus();
</script>