<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchMaterialObject */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Материальные ценнности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-object-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php /*Html::a('Добавить объект', ['create'], ['class' => 'btn btn-success'])*/ ?>
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

            //'name',
            ['attribute' => 'nameLink', 'format' => 'raw'],
            //'photo_local',
            //'photo_cloud',
            //'count',
            //'price',
            //'number',
            ['attribute' => 'attribute'/*, 'filter' => [ 0 => 'ОС', 1 => 'ТМЦ']*/],
            //'finance_source_id',
            ['attribute' => 'inventory_number', 'label' => 'Инвентарный номер'],
            ['attribute' => 'id', 'label' => 'Внутренний рег. номер'],
            //'typeString',
            //'is_education',
            //'state',
            //'damage',
            ['attribute' => 'status', 'value' => function($model){
                return $model->status == 1 ? 'Рабочий' : 'Нерабочий';
            }, 'filter' => [ 0 => 'Нерабочий', 1 => 'Рабочий']],
            //'write_off',
            //'lifetime',
            //'expiration_date',
            //'create_date',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>


</div>
