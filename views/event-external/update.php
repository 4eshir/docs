<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\EventExternalWork */

$this->title = 'Редактировать отчетное мероприятие: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Отчетные мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="event-external-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
