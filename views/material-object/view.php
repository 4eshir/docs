<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Материальные ценности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="material-object-view">

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

            'unique_id',
            'name',
            'acceptance_date',
            'balance_price',
            'count',
            ['attribute' => 'main', 'value' => function($model) { return $model->main === 1 ? 'Да' : 'Нет'; }],
            ['attribute' => 'currentResp', 'format' => 'raw'],
            ['attribute' => 'filesLink', 'format' => 'raw'],
        ],
    ]) ?>

</div>
