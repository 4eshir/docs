<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentOrder */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить приказ', ['create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить резерв', ['create-reserve'], ['class' => 'btn btn-warning', 'style' => 'display: inline-block;']) ?>
    </p>

    <?php

    $gridColumns = [
        ['attribute' => 'order_date', 'label' => 'Дата приказа'],
        ['attribute' => 'order_number', 'label' => 'Номер приказа'],
        ['attribute' => 'order_name', 'label' => 'Наименование приказа'],
        ['attribute' => 'signedName', 'label' => 'Кем подписан', 'value' => function($model)
        {
            return $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'. '.mb_substr($model->signed->patronymic, 0, 1);
        }],
        ['attribute' => 'bringName', 'label' => 'Проект вносит', 'value' => function($model)
        {
            return $model->bring->secondname.' '.mb_substr($model->bring->firstname, 0, 1).'. '.mb_substr($model->bring->patronymic, 0, 1);
        }],
        ['attribute' => 'executorName', 'label' => 'Исполнитель', 'value' => function($model)
        {
            return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1);
        }],
        ['attribute' => 'responsiblies', 'label' => 'Ответственные', 'value' => function($model)
        {
            $resp = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
            $result = '';
            foreach ($resp as $respOne)
                $result = $result.$respOne->people->secondname.' '.mb_substr($respOne->people->firstname, 0, 1).'. '.mb_substr($respOne->people->patronymic, 0, 1).'. ';
            return $result;
        }],
        ['attribute' => 'registerName', 'label' => 'Регистратор приказа', 'value' => function($model)
        {
            return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1);
        }],
    ];
    echo '<b>Скачать файл </b>';
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
            ['attribute' => 'order_date', 'label' => 'Дата приказа'],
            ['attribute' => 'order_number', 'label' => 'Номер приказа', 'value' => function($model){
                if ($model->order_postfix == null)
                    return $model->order_number.'/'.$model->order_copy_id;
                else
                    return $model->order_number.'/'.$model->order_copy_id.'/'.$model->order_postfix;
            }],
            ['attribute' => 'order_name', 'label' => 'Наименование приказа'],
            ['attribute' => 'bringName','label' => 'Проект вносит', 'value' => function ($model) {
                return $model->bring->secondname.' '.mb_substr($model->bring->firstname, 0, 1).'.'.mb_substr($model->bring->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'executorName','label' => 'Исполнитель', 'value' => function ($model) {
                return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'.'.mb_substr($model->executor->patronymic, 0, 1).'.';
            },
            ],

            ['attribute' => 'responsibilities','label' => 'Ответственные', 'contentOptions' => ['encode' => 'false'], 'value' => function ($model) {
                $tmp = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
                $result = '';
                for ($i = 0; $i < count($tmp); $i++)
                    $result = $result.$tmp[$i]->people->secondname.' '.mb_substr($tmp[$i]->people->firstname, 0, 1).'.'.mb_substr($tmp[$i]->people->patronymic, 0, 1).'. <br>';

                return $result;
            }, 'format' => 'html'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
