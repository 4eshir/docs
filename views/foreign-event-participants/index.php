<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchForeignEventParticipants */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Участники деятельности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить нового участника деятельности', ['create'], ['class' => 'btn btn-success']) ?> <?= Html::a('Загрузить участников из файла', ['file-load'], ['class' => 'btn btn-primary']) ?>
    </p>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'secondname',
            'firstname',
            'patronymic',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
