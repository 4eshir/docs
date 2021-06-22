<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTemporaryJournal */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Temporary Journals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temporary-journal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Temporary Journal', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'material_object_id',
            'give_people_id',
            'gain_people_id',
            'date_issue',
            //'approximate_time:datetime',
            //'date_delivery',
            //'branch_id',
            //'auditorium_id',
            //'event_id',
            //'foreign_event_id',
            //'signed_give',
            //'signed_gain',
            //'comment',
            //'files',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
