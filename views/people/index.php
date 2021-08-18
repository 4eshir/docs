<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchPeople */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Люди';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить человека', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'secondname', 'label' => 'Фамилия'],
            ['attribute' => 'firstname', 'label' => 'Имя'],
            ['attribute' => 'patronymic', 'label' => 'Отчество'],
            ['attribute' => 'positionName', 'label' => 'Должность', 'value' => function($model){
                return $model->position->name;
            }],
            /*['attribute' => 'positionsWork', 'label' => 'Должность', 'format' => 'raw'],*/ // вот это работает, но нужно подшаманить с фильтрами
            ['attribute' => 'companyName', 'label' => 'Организация', 'value' => function($model){
                return $model->company->name;
            }],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
