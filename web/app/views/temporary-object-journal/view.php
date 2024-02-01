<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryObjectJournal */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Temporary Object Journals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="temporary-object-journal-view">

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
            'user_give_id',
            'user_get_id',
            'confirm_give',
            'confirm_get',
            'material_object_id',
            'container_id',
            'comment',
            'date_give',
            'date_get',
            'real_date_get',
        ],
    ]) ?>

</div>
