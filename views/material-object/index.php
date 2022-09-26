<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchMaterialObject */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Материальные ценнности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-object-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Material Object', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'photo_local',
            'photo_cloud',
            'count',
            //'price',
            //'number',
            //'attribute',
            //'finance_source_id',
            //'inventory_number',
            //'type',
            //'is_education',
            //'state',
            //'damage',
            //'status',
            //'write_off',
            //'lifetime',
            //'expiration_date',
            //'create_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
