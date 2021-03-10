<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\TrainingGroup */

$this->title = 'Редактировать учебную группу: ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Группа '.$model->number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="training-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
        'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
        'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
    ]) ?>

</div>
