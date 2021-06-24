<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\EventFormWork */

$this->title = 'Редактировать форму мероприятий: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Формы мероприятий', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="event-form-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
