<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchAsAdmin */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'As Admins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="as-admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create As Admin', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'id', 'label' => '№ п/п'],
            ['attribute' => 'as_name', 'label' => 'Наименование'],
            ['attribute' => 'requisites', 'label' => 'Реквизиты', 'value' => function($model){
                return $model->company->name.' '.$model->document_number.' '.$model->document_date;
            }],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
