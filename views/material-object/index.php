<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchMaterialObject */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Материальные ценности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-object-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить мат. ценность', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'unique_id',
            'name',
            'acceptance_date',
            'balance_price',
            'count',
            ['attribute' => 'currentResp', 'format' => 'raw'],
            //'main',
            //'files',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
