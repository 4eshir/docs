<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchRegulation */
/* @var $dataProvider yii\data\ActiveDataProvider */

$session = Yii::$app->session;
$this->title = \app\models\work\RegulationTypeWork::find()->where(['id' => $session->get('type')])->one()->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить положение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    $gridColumns = [
        ['attribute' => 'date', 'label' => 'Дата положения'],
        ['attribute' => 'name'],
        ['attribute' => 'orderString', 'label' => 'Приказ', 'value' => function($model){
            $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
            $doc_num = 0;
            if ($order->order_postfix == null)
                $doc_num = $order->order_number.'/'.$order->order_copy_id;
            else
                $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
            return 'Приказ №'.$doc_num.' "'.$order->order_name.'"';
        }],
        ['attribute' => 'ped_council_number', 'label' => '№ пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
        ['attribute' => 'ped_council_date', 'label' => 'Дата пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
        ['attribute' => 'par_council_number', 'label' => '№ совета<br>род.', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
        ['attribute' => 'par_council_date', 'label' => 'Дата совета<br>род.', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
        ['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
            if ($model->state == 1)
                return 'Актуально';
            else
            {
                $exp = \app\models\work\ExpireWork::find()->where(['expire_order_id' => $model->order_id])->one();
                if ($exp == null)
                    $exp = \app\models\work\ExpireWork::find()->where(['expire_regulation_id' => $model->id])->one();
                $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $exp->active_regulation_id])->one();
                $doc_num = 0;

                if ($order->order_postfix == null)
                    $doc_num = $order->order_number.'/'.$order->order_copy_id;
                else
                    $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                return 'Утратило силу в связи с приказом №'.$doc_num;
            }
        }],
    ];
    echo '<b>Скачать файл </b>';
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'options' => [
            'padding-bottom: 100px',
        ]
    ]);

    ?>
    <div style="margin-bottom: 10px">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['state'] == 0)
                return ['style' => 'background: #c0c0c0']; //return ['class' => 'danger'];
            else
                return ['class' => 'default'];
        },
        'columns' => [

            ['attribute' => 'date', 'label' => 'Дата положения'],
            ['attribute' => 'name'],
            ['attribute' => 'orderString', 'label' => 'Приказ', 'value' => function($model){
                $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                $doc_num = 0;
                if ($order->order_postfix == null)
                    $doc_num = $order->order_number.'/'.$order->order_copy_id;
                else
                    $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                return 'Приказ №'.$doc_num.' "'.$order->order_name.'"';
            }],
            ['attribute' => 'ped_council_number', 'label' => '№ пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
            ['attribute' => 'ped_council_date', 'label' => 'Дата пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
            ['attribute' => 'par_council_number', 'label' => '№ совета<br>род.', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
            ['attribute' => 'par_council_date', 'label' => 'Дата совета<br>род.', 'encodeLabel' => false, 'format' => 'raw', 'visible' => $session->get('type') == 1],
            ['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
                if ($model->state == 1)
                    return 'Актуально';
                else
                {
                    $exp = \app\models\work\ExpireWork::find()->where(['expire_order_id' => $model->order_id])->one();
                    if ($exp == null)
                        $exp = \app\models\work\ExpireWork::find()->where(['expire_regulation_id' => $model->id])->one();
                    $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $exp->active_regulation_id])->one();
                    $doc_num = 0;

                    if ($order->order_postfix == null)
                        $doc_num = $order->order_number.'/'.$order->order_copy_id;
                    else
                        $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                    return 'Утратило силу в связи с приказом '.Html::a('№'.$doc_num, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
                }
            }, 'format' => 'raw', 'filter' => [1 => "Актуально", 0 => "Утратило силу"]],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
