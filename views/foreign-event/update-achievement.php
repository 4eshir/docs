<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\work\TeacherParticipantBranchWork;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\work\TeacherParticipantWork */

$this->title = 'Редактировать: ' . $model->participantWork->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->participantWork->fullName, 'url' => ['foreign-event-participants/view', 'id' => $model->participant_id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="teacher-participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php

    echo $form->field($model, 'cert_number')->textInput();

    ?>

    <?php

    echo $form->field($model, 'nomination')->textInput();

    ?>

    <?php

    echo $form->field($model, 'achievment')->textInput();

    ?>

    <?= $form->field($model, 'date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control date_achieve',
            'autocomplete'=>'off'

        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]]) ?>

    <?php

    echo $form->field($model, 'winner')->checkbox();

    ?>

    <div class="form-group">
        <div class="button">

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>