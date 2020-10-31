<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentOut */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Исходящая документация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-out-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить исходящий документ', ['docs-out/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    $gridColumns = [
        ['attribute' => 'document_number', 'label' => 'Номер документа'],
        ['attribute' => 'document_date', 'label' => 'Дата документа'],
        ['attribute' => 'document_theme', 'label' => 'Тема документа'],
        ['attribute' => 'companyName', 'label' => 'Корреспондент', 'value' => function($model)
        {
            return $model->position->name.' '.$model->company->name;
        }],
        ['attribute' => 'signedName', 'label' => 'Кем подписан', 'value' => function($model)
        {
            return $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'. '.mb_substr($model->signed->patronymic, 0, 1);
        }],
        ['attribute' => 'executorName', 'label' => 'Кто исполнил', 'value' => function($model)
        {
            return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1);
        }],
        ['attribute' => 'registerName', 'label' => 'Кто исполнил', 'value' => function($model)
        {
            return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1);
        }],
        ['attribute' => 'sendMethod.name', 'label' => 'Способ отправки'],
        ['attribute' => 'sent_date', 'label' => 'Дата отправки'],
    ];
    echo '<b>Скачать Excel </b>';
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'options' => [
                'padding-bottom: 100px',
        ]
    ]);

    ?>
    <div style="margin-bottom: 10px">

    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [

            ['attribute' => 'document_number','label' => '№ п/п'],
            ['attribute' => 'document_date','label' => 'Дата документа'],
            ['attribute' => 'document_theme','label' => 'Тема документа'],
            ['attribute' => 'companyName','label' => 'Кому адресован', 'value' => function ($model) {
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
            ['attribute' => 'registerName','label' => 'Кто регистрировал', 'value' => function ($model) {
                return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'sendMethodName','label' => 'Способ отправления', 'value' => 'sendMethod.name'],
            ['attribute' => 'sent_date','label' => 'Дата отправления'],
            /*['attribute' => 'Scan','label' => 'Скан документа', 'value' => function ($model) {
                return Html::a($model->Scan, \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $model->Scan]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],*/


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
