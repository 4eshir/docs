<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\PeopleWork */

$this->title = 'Редактировать человека: ' . $model->secondname.' '.$model->firstname.' '.$model->patronymic;
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->secondname.' '.$model->firstname.' '.$model->patronymic, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="people-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
    ]) ?>

</div>
