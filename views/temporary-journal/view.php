<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryJournal */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Temporary Journals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="temporary-journal-view">

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
            'material_object_id',
            'give_people_id',
            'gain_people_id',
            'date_issue',
            'approximate_time:datetime',
            'date_delivery',
            'branch_id',
            'auditorium_id',
            'event_id',
            'foreign_event_id',
            'signed_give',
            'signed_gain',
            'comment',
            'files',
        ],
    ]) ?>

</div>
