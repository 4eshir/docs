<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\Event */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

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
            'start_date',
            'finish_date',
            ['attribute' => 'event_type_id', 'value' => $model->eventType->name],
            ['attribute' => 'event_form_id', 'value' => $model->eventForm->name],
            'address',
            ['attribute' => 'event_level_id', 'value' => $model->eventLevel->name],
            'participants_count',
            ['attribute' => 'is_federal', 'value' => function($model){
                if ($model->is_federal == 1)
                    return 'Да';
                else
                    return 'Нет';
            }],
            ['attribute' => 'responsible_id', 'value' => $model->responsible->shortName],
            'key_words',
            'comment',
            ['attribute' => 'order_id', 'value' => Html::a($model->order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $model->order_id])),
                'format' => 'raw'],
            ['attribute' => 'regulation_id', 'value' => Html::a($model->regulation->name, \yii\helpers\Url::to(['regulation/view', 'id' => $model->regulation_id])),
                'format' => 'raw'],
            'protocol',
            'photos',
            'reporting_doc',
            'other_files',
        ],
    ]) ?>

</div>
