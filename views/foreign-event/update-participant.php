<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => ''
    ];
    echo $form->field($model, 'teacher_id')->dropDownList($items,$params)->label('ФИО педагогов');
    echo $form->field($model, 'teacher2_id')->dropDownList($items,$params)->label(false);
    ?>

    <?php
    $branchs = \app\models\work\BranchWork::find()->orderBy(['id' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
    echo $form->field($model, 'branchs')->checkboxList(
            $items, ['class' => 'base',
                'item' => function ($index, $label, $name, $checked, $value) {
                if ($checked == 1) $checked = 'checked';
                return
                    '<div class="checkbox" class="form-control">
                            <label style="margin-bottom: 0px" for="branch-' . $index .'">
                                <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                '. $label .'
                            </label>
                        </div>';
            }]
    )->label('<u>Отдел(-ы)</u>')
    ?>

    <?= $form->field($model, 'team')->textInput()->label('Команда') ?>

    <?= $form->field($model, 'file')->fileInput()->label('Представленные материалы') ?>

    <?php
    $partFiles = \app\models\work\ParticipantFilesWork::find()->where(['participant_id' => $model->participant_id])->andWhere(['foreign_event_id' => $model->foreign_event_id])->one();
    if ($partFiles !== null)
        echo '<h5>Загруженный файл: '.Html::a($partFiles->filename, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $partFiles->filename, 'type' => 'participants'])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['foreign-event/delete-file', 'fileName' => $partFiles->filename, 'modelId' => $partFiles->id, 'type' => 'participants'])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
