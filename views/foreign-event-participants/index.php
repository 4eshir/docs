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
        <?= Html::a('Добавить нового участника деятельности', ['create'], ['class' => 'btn btn-success']) ?> <?= Html::a('Загрузить участников из файла', ['file-load'], ['class' => 'btn btn-primary']) ?> <?= Html::a('Проверить участников на некорректные данные', ['check-correct'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?php
    echo '<div style="margin-bottom: 10px">'.Html::a('Показать участников с некорректными данными', \yii\helpers\Url::to(['foreign-event-participants/index', 'sort' => '1'])).'</div>';
    ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'rowOptions' => function($data) {
            if ($data['sex'] == 'Другое')
                return ['class' => 'danger'];
            else if (($data['is_true'] == 0 || $data['is_true'] == 2) && $data['guaranted_true'] !== 1)
                return ['class' => 'warning'];
            else
                return ['class' => 'default'];
        },
        'columns' => [

            'secondname',
            'firstname',
            'patronymic',
            'sex',
            ['attribute' => 'birthdate', 'value' => function($model){return date("d.m.Y", strtotime($model->birthdate));}],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
