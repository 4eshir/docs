<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchForeignEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Участие во внешних мероприятиях';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="foreign-event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить внешнее мероприятие', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'name',
            ['attribute' => 'companyString'],
            'start_date',
            'finish_date',
            'city',
            'eventWayString',
            'eventLevelString',
            ['attribute' => 'teachers', 'format' => 'raw', 'contentOptions' => ['class' => 'text-nowrap']],
            'participantCount',
            ['attribute' => 'winners', 'format' => 'raw'],
            ['attribute' => 'prizes', 'format' => 'raw'],
            'businessTrips',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
