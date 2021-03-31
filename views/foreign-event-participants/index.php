<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchForeignEventParticipants */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Участники образовательной деятельности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить нового участника образовательной деятельности', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'firstname',
            'secondname',
            'patronymic',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
