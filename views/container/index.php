<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchContainer */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Контейнеры';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить контейнер', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            $err = $data['errorsWork'] !== '';
            if ($err)
                return ['class' => 'danger'];
            else
                return ['class' => 'default'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            ['attribute' => 'containerLink', 'format' => 'raw'],
            ['attribute' => 'objectLink', 'format' => 'raw'],
            ['attribute' => 'auditoriumLink', 'format' => 'raw'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
