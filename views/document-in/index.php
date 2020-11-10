<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentIn */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Входящая документация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-in-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить входящий документ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= $this->render('_search', ['model' => $searchModel]) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [

                ['attribute' => 'id', 'label' => '№ п/п', 'encodeLabel' => false],
                ['attribute' => 'local_date', 'label' => 'Дата поступления<br>документа', 'encodeLabel' => false],
                ['attribute' => 'real_date', 'label' => 'Дата входящего<br>документа', 'encodeLabel' => false],
                ['attribute' => 'real_number', 'label' => 'Рег. номер<br>входящего док.', 'encodeLabel' => false],

                ['attribute' => 'positionName', 'label' => 'Наименование<br>корреспондента', 'encodeLabel' => false, 'value' => function ($model) {
                    return $model->position->name.' '.$model->company->name;
                }],
                ['attribute' => 'correspondent_id', 'label' => 'Кем подписано', 'value' => 'correspondent.fullName'],
                ['attribute' => 'document_theme', 'label' => 'Краткое содержание<br>документа', 'encodeLabel' => false],
                ['attribute' => 'sendMethodName','label' => 'Способ отправления', 'value' => 'sendMethod.name'],


                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

