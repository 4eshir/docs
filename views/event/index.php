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

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['order_id'] == null)
                return ['class' => 'danger'];
            else
                return ['class' => 'default'];
        },
        'columns' => [

            ['attribute' => 'name'],
            ['attribute' => 'start_date'],
            ['attribute' => 'finish_date'],
            ['attribute' => 'event_type_id', 'value' => function($model){
                return \app\models\work\EventTypeWork::find()->where(['id' => $model->event_type_id])->one()->name;
            }, 'filter' => [ 1 => "Соревновательный", 2 => "Несоревновательный"]],
            ['attribute' => 'address'],
            ['attribute' => 'eventLevelString', 'label' => 'Уровень<br>мероприятия', 'value' => function($model){
                return \app\models\work\EventLevelWork::find()->where(['id' => $model->event_level_id])->one()->name;
            }, 'encodeLabel' => false],
            ['attribute' => 'participants_count'],
            ['attribute' => 'is_federal', 'value' => function($model){
                if ($model->is_federal == 1)
                    return 'Да';
                else
                    return 'Нет';
            }, 'filter' => [ 1 => "Да", 0 => "Нет"]],
            ['attribute' => 'responsibleString', 'label' => 'Ответственный(-ые) работник(-и)'],
            ['attribute' => 'order_id', 'value' => function($model){
                $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                if ($order == null)
                    return 'Нет';
                return Html::a('№'.$order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
            }, 'format' => 'raw'],
            ['attribute' => 'regulation_id', 'value' => function($model){
                $reg = \app\models\work\RegulationWork::find()->where(['id' => $model->regulation_id])->one();
                if ($reg == null)
                    return 'Нет';
                return Html::a('Положение "'.$reg->name.'"', \yii\helpers\Url::to(['regulation/view', 'id' => $reg->id]));
            }, 'format' => 'raw'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
