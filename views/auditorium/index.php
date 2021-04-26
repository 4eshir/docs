<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchAuditorium */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Помещения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditorium-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить помещение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'name',
            'square',
            ['attribute' => 'isEducation', 'label' => 'Предназначен для обр. деят.'],
            ['attribute' => 'branchLink', 'label' => 'Название отдела', 'format' => 'html'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
