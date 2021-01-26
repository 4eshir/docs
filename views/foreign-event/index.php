<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchForeignEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Foreign Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Foreign Event', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'name',
            'company_id',
            'start_date',
            'finish_date',
            'city',
            'event_way_id',
            'event_level_id',
            'min_participants_age',
            'max_participants_age',
            'order_participation_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
