<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentOrder */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Document Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Document Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_number',
            'order_name',
            'order_date',
            'signed_id',
            'bring_id',
            'executor_id',
            //'scan',
            'register_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
