<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTemporaryObjectJournal */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Temporary Object Journals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temporary-object-journal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Temporary Object Journal', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_give_id',
            'user_get_id',
            'confirm_give',
            'confirm_get',
            //'material_object_id',
            //'container_id',
            //'comment',
            //'date_give',
            //'date_get',
            //'real_date_get',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
