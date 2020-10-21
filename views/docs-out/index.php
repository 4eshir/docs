<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentOut */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Document Outs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-out-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Document Out', ['docs-out/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'document_number','label' => '№ документа'],
            ['attribute' => 'document_date','label' => 'Дата документа'],
            ['attribute' => 'document_name','label' => 'Название документа'],
            ['attribute' => 'document_theme','label' => 'Тема документа'],
            ['attribute' => 'companyName','label' => 'Корреспондент', 'value' => function ($model) {
                return $model->position->name.' '.$model->company->name;
            },
            ],
            ['attribute' => 'signedName','label' => 'Кем подписан', 'value' => function ($model) {
                return $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'.'.mb_substr($model->signed->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'executorName','label' => 'Кто исполнитель', 'value' => function ($model) {
                return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'.'.mb_substr($model->executor->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'sendMethodName','label' => 'Способ отправления', 'value' => 'sendMethod.name'],
            ['attribute' => 'sent_date','label' => 'Дата отправления'],
            ['attribute' => 'Scan','label' => 'Скан документа'],
            ['attribute' => 'registerName','label' => 'Кто регистрировал', 'value' => function ($model) {
                return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
            },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
