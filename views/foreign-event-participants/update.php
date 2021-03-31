<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEventParticipants */

$this->title = 'Редактировать участника образовательной деятельности: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Участники образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="foreign-event-participants-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
