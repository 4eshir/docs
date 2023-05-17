<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchContract */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Договоры';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать договор', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

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
            //['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'number', 'label' => 'Номер договора'],
            ['attribute' => 'date', 'label' => 'Дата договора'],

            //'file',
            //'key_words',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
