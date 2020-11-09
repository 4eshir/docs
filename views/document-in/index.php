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

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div style="overflow: auto; overflow-y: scroll; Height: 500px; Width:100%">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [

                'id',
                ['attribute' => 'local_number', 'label' => 'Локальный<br>номер', 'encodeLabel' => false],
                ['attribute' => 'local_date', 'label' => 'Локальная<br>дата', 'encodeLabel' => false],
                ['attribute' => 'real_number', 'label' => 'Исходящий<br>номер', 'encodeLabel' => false],
                ['attribute' => 'real_date', 'label' => 'Исходящая дата'],
                ['attribute' => 'positionName', 'label' => 'Наименование<br>корреспондента', 'encodeLabel' => false, 'value' => function ($model) {
                    return $model->position->name.' '.$model->company->name;
                }],
                ['attribute' => 'document_theme', 'label' => 'Тема документа'],
                ['attribute' => 'signedString', 'label' => 'Регистратор', 'value' => function ($model) {
                    return $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'.'.mb_substr($model->signed->patronymic, 0, 1).'.';
                }],
                ['attribute' => 'target', 'label' => 'Кому адресован'],
                ['attribute' => 'getString', 'label' => 'Кем получен', 'value' => function ($model) {
                    return $model->get->secondname.' '.mb_substr($model->get->firstname, 0, 1).'.'.mb_substr($model->get->patronymic, 0, 1).'.';
                }],
                ['attribute' => 'sendMethodName','label' => 'Способ отправления', 'value' => 'sendMethod.name'],
                ['attribute' => 'scan', 'label' => 'Скан документа'],
                ['attribute' => 'applications', 'label' => 'Приложения'],
                ['attribute' => 'registerString', 'label' => 'Регистратор', 'value' => function ($model) {
                    return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
                }],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

</div>
