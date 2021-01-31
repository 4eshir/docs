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
    <?php
    if ($model->business_trip == 0)
    { ?>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'companyString',
                'start_date',
                'finish_date',
                'city',
                'eventWayString',
                'eventLevelString',
                ['attribute' => 'participantsLink', 'format' => 'raw'],
                ['attribute' => 'achievementsLink', 'format' => 'raw'],
                'ageRange',
                'businessTrip',

                ['attribute' => 'orderParticipationString', 'format' => 'raw'],

                'key_words',
                ['attribute' => 'docString', 'format' => 'raw'],
            ],
        ]) ?>
    <?php }
    else
    { ?>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'companyString',
                'start_date',
                'finish_date',
                'city',
                'eventWayString',
                'eventLevelString',
                ['attribute' => 'participantsLink', 'format' => 'raw'],
                ['attribute' => 'achievementsLink', 'format' => 'raw'],
                'ageRange',
                'businessTrip',
                ['attribute' => 'escort_id', 'value' => function ($model) { return $model->escort->shortName; }],
                ['attribute' => 'orderBusinessTripString', 'format' => 'raw'],

                ['attribute' => 'orderParticipationString', 'format' => 'raw'],

                'key_words',
                ['attribute' => 'docString', 'format' => 'raw'],
            ],
        ]) ?>
    <?php
    }
    ?>


</div>
