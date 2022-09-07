<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchEventForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Формы мероприятий';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности / Формы мероприятий / Отчетные мероприятия', 'url' => ['dictionaries/studies']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-form-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить форму мероприятий', ['create'], ['class' => 'btn btn-success']) ?>
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
