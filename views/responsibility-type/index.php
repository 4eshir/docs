<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchResponsibilityType */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Виды ответственности';
$this->params['breadcrumbs'][] = ['label' => 'Отделы / Помещения / Виды ответственности', 'url' => ['dictionaries/premises']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="responsibility-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить вид ответственности', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
