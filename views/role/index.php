<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchRole */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Роли';
$this->params['breadcrumbs'][] = ['label' => 'Список пользователей / Роли', 'url' => ['dictionaries/users']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать роль', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'name', 'label' => 'Название роли'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
