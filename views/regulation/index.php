<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchRegulation */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Положения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить положение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['state'] == 0)
                return ['class' => 'danger'];
            else
                return ['class' => 'default'];
        },
        'columns' => [

            ['attribute' => 'date', 'label' => 'Дата положения'],
            ['attribute' => 'name', 'label' => 'Тема положения'],
            ['attribute' => 'order_id', 'label' => 'Приказ', 'value' => function($model){
                $order = \app\models\common\DocumentOrder::find()->where(['id' => $model->order_id])->one();
                $doc_num = 0;
                if ($order->order_postfix == null)
                    $doc_num = $order->order_number.'/'.$order->order_copy_id;
                else
                    $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                return 'Приказ №'.$doc_num.' "'.$order->order_name.'"';
            }],
            ['attribute' => 'ped_council_number', 'label' => '№ пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'ped_council_date', 'label' => 'Дата пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'par_council_number', 'label' => '№ род.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'par_council_date', 'label' => 'Дата род.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
                if ($model->state == 1)
                    return 'Актуально';
                else
                {
                    $order = \app\models\common\DocumentOrder::find()->where(['id' => $model->order_id])->one();
                    $doc_num = 0;
                    if ($order->order_postfix == null)
                        $doc_num = $order->order_number.'/'.$order->order_copy_id;
                    else
                        $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                    return 'Утратило силу в связи с приказом №'.$doc_num;
                }
            }],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
