<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEventParticipants */

$this->title = 'Update Foreign Event Participants: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Foreign Event Participants', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="foreign-event-participants-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
