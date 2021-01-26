<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEvent */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Foreign Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="foreign-event-view">

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
            'company_id',
            'start_date',
            'finish_date',
            'city',
            'event_way_id',
            'event_level_id',
            'min_participants_age',
            'max_participants_age',
            'business_trip',
            'escort_id',
            'order_participation_id',
            'order_business_trip_id',
            'key_words',
            'docs_achievement',
        ],
    ]) ?>

</div>
