<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мероприятия';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить мероприятие', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'name'],
            ['attribute' => 'start_date'],
            ['attribute' => 'finish_date'],
            ['attribute' => 'event_type_id', 'value' => function($model){
                return \app\models\common\EventType::find()->where(['id' => $model->event_type_id])->one()->name;
            }],
            ['attribute' => 'event_form_id', 'value' => function($model){
                return \app\models\common\EventForm::find()->where(['id' => $model->event_form_id])->one()->name;
            }],
            ['attribute' => 'address'],
            ['attribute' => 'event_level_id', 'value' => function($model){
                return \app\models\common\EventLevel::find()->where(['id' => $model->event_level_id])->one()->name;
            }],
            ['attribute' => 'participants_count'],
            ['attribute' => 'is_federal', 'value' => function($model){
                if ($model->is_federal == 1)
                    return 'Да';
                else
                    return 'Нет';
            }],
            ['attribute' => 'responsible_id', 'value' => function($model){
                return \app\models\common\People::find()->where(['id' => $model->responsible_id])->one()->shortName;
            }],
            ['attribute' => 'order_id', 'value' => function($model){
                $order = \app\models\common\DocumentOrder::find()->where(['id' => $model->order_id])->one();
                return Html::a('№'.$order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
            }, 'format' => 'raw'],
            ['attribute' => 'regulation_id', 'value' => function($model){
                $reg = \app\models\common\Regulation::find()->where(['id' => $model->regulation_id])->one();
                return Html::a('Положение "'.$reg->name.'"', \yii\helpers\Url::to(['regulation/view', 'id' => $reg->id]));
            }, 'format' => 'raw'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
