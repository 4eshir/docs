<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTrainingGroup */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Training Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Training Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
                
            'number',
            ['attribute' => 'programName', 'format' => 'html'],
            'teacherName',
            'start_date',
            'finish_date',
            'openText',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
