<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\User */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'secondname',
            'firstname',
            'patronymic',
            'username',
            ['attribute' => 'addUsers', 'value' => function($model) {if ($model->addUsers == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewRoles', 'value' => function($model) {if ($model->viewRoles == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editRoles', 'value' => function($model) {if ($model->editRoles == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewOut', 'value' => function($model) {if ($model->viewOut == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editOut', 'value' => function($model) {if ($model->editOut == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewIn', 'value' => function($model) {if ($model->viewIn == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editIn', 'value' => function($model) {if ($model->editIn == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewOrder', 'value' => function($model) {if ($model->viewOrder == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editOrder', 'value' => function($model) {if ($model->editOrder == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewRegulation', 'value' => function($model) {if ($model->viewRegulation == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editRegulation', 'value' => function($model) {if ($model->editRegulation == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewEvent', 'value' => function($model) {if ($model->viewEvent == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editEvent', 'value' => function($model) {if ($model->editEvent == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewAS', 'value' => function($model) {if ($model->viewAS == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editAS', 'value' => function($model) {if ($model->editAS == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'viewAdd', 'value' => function($model) {if ($model->viewAdd == 1) return 'Да'; else return 'Нет';}],
            ['attribute' => 'editAdd', 'value' => function($model) {if ($model->editAdd == 1) return 'Да'; else return 'Нет';}],
        ],
    ]) ?>

</div>
