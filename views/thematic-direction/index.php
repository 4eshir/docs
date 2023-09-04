<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchThematicDirection */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тематические направления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thematic-direction-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить тематическое направление', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'full_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
