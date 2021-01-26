<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchForeignEventParticipants */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Foreign Event Participants';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Foreign Event Participants', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'firstname',
            'secondname',
            'patronymic',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
