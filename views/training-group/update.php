<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\TrainingGroup */

$this->title = 'Update Training Group: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Training Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="training-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant
    ]) ?>

</div>
