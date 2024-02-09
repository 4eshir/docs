<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchHistoryObject */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'History Objects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-object-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create History Object', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'material_object_id',
            'count',
            'container_id',
            'history_transaction_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
