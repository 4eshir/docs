<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\Container */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Контейнеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
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
            'name',
            ['attribute' => 'containerLink', 'format' => 'raw'],
            ['attribute' => 'objectLink', 'format' => 'raw'],
            ['attribute' => 'auditoriumLink', 'format' => 'raw'],
            ['attribute' => 'objectsInContainer', 'format' => 'raw'],
        ],
    ]) ?>

</div>
