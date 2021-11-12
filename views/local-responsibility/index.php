<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchLocalResponsibility */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учет ответственности работников';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="local-responsibility-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить ответственность', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['people_id'] === null)
                return ['class' => 'danger'];
            else
                return ['class' => 'default'];
        },
        'columns' => [

            ['attribute' => 'responsibilityTypeStr', 'format' => 'raw'],
            ['attribute' => 'branchStr', 'format' => 'raw'],
            ['attribute' => 'auditoriumStr', 'format' => 'raw'],
            ['attribute' => 'peopleStr', 'format' => 'raw'],
            ['attribute' => 'orderStr', 'format' => 'raw', 'label' => 'Приказ'],
            ['attribute' => 'regulationStr', 'format' => 'raw'],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
