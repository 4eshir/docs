<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Material Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="material-object-view">

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
            'id',
            'name',
            'photo_local',
            'photo_cloud',
            'count',
            'price',
            'number',
            'attribute',
            'finance_source_id',
            'inventory_number',
            'type',
            'is_education',
            'state',
            'damage',
            'status',
            'write_off',
            'lifetime',
            'expiration_date',
            'create_date',
        ],
    ]) ?>

</div>
