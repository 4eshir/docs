<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Образовательные программы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-program-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить программу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'name',
            ['attribute' => 'ped_council_date', 'label' => 'Дата пед. сов.'],
            ['attribute' => 'ped_council_number', 'label' => '№ пед. сов.'],
            'authorName',
            'capacity',
            'studentAge',
            'focus',
            ['attribute' => 'allowRemote', 'label' => 'Дист. тех.'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
