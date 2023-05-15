<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchInvoice */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Документы о поступлении материальных объектов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить документ', ['create'], ['class' => 'btn btn-success']) ?>
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
            ['attribute' => 'contractorString', 'format' => 'raw'],
            ['attribute' => 'contractString', 'format' => 'raw'],
            ['attribute' => 'numberString', 'format' => 'raw'],
            'date_invoice',
            'date_product',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
