<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchEventExternal */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчетные мероприятия';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности / Формы мероприятий / Отчетные мероприятия', 'url' => ['dictionaries/studies']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-external-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить отчетное мероприятие', ['create'], ['class' => 'btn btn-success']) ?>
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
