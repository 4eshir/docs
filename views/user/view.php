<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\User */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<style>
    .badge {
        padding: 1px 9px 2px;
        font-size: 12.025px;
        font-weight: bold;
        white-space: nowrap;
        color: #ffffff;
        background-color: #999999;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .badge:hover {
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }
    .badge-error {
        background-color: #b94a48;
    }
    .badge-error:hover {
        background-color: #953b39;
    }
    .badge-success {
        background-color: #468847;
    }
    .badge-success:hover {
        background-color: #356635;
    }

    table.detail-view th {
        width: 50%;
    }

    table.detail-view td {
        width: 50%;
    }
</style>

<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить этого пользователя?',
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
            'email',
            ['attribute' => 'addUsers', 'value' => function($model) {if ($model->addUsers == 1) return '<span class="badge badge-success">Да</span>';
                else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewRoles', 'value' => function($model) {if ($model->viewRoles == 1) return '<span class="badge badge-success">Да</span>';
                else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editRoles', 'value' => function($model) {if ($model->editRoles == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewOut', 'value' => function($model) {if ($model->viewOut == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editOut', 'value' => function($model) {if ($model->editOut == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewIn', 'value' => function($model) {if ($model->viewIn == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editIn', 'value' => function($model) {if ($model->editIn == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewOrder', 'value' => function($model) {if ($model->viewOrder == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editOrder', 'value' => function($model) {if ($model->editOrder == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewRegulation', 'value' => function($model) {if ($model->viewRegulation == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editRegulation', 'value' => function($model) {if ($model->editRegulation == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewEvent', 'value' => function($model) {if ($model->viewEvent == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editEvent', 'value' => function($model) {if ($model->editEvent == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewAS', 'value' => function($model) {if ($model->viewAS == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editAS', 'value' => function($model) {if ($model->editAS == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewAdd', 'value' => function($model) {if ($model->viewAdd == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editAdd', 'value' => function($model) {if ($model->editAdd == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewForeign', 'value' => function($model) {if ($model->viewForeign == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editForeign', 'value' => function($model) {if ($model->editForeign == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
        ],
    ]) ?>

</div>
