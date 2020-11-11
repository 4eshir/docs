<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\People */

$this->title = $model->secondname.' '.$model->firstname.' '.$model->patronymic;
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="people-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого человека?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Фамилия', 'attribute' => 'secondname'],
            ['label' => 'Имя', 'attribute' => 'firstname'],
            ['label' => 'Отчество', 'attribute' => 'patronymic'],
            ['label' => 'Должность', 'attribute' => 'position', 'value' => function($model){
                return $model->position->name;
            }],
            ['label' => 'Организация', 'attribute' => 'company', 'value' => function($model){
                return $model->company->name;
            }],
        ],
    ]) ?>

</div>
