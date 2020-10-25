<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentOrder */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить приказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'order_number', 'label' => 'Номер приказа'],
            ['attribute' => 'order_name', 'label' => 'Название приказа'],
            ['attribute' => 'order_date', 'label' => 'Дата приказа'],
            ['attribute' => 'signedName','label' => 'Кем подписан', 'value' => function ($model) {
                return $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'.'.mb_substr($model->signed->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'executorName','label' => 'Исполнитель', 'value' => function ($model) {
                return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'.'.mb_substr($model->executor->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'bringName','label' => 'Проект вносит', 'value' => function ($model) {
                return $model->bring->secondname.' '.mb_substr($model->bring->firstname, 0, 1).'.'.mb_substr($model->bring->patronymic, 0, 1).'.';
            },
            ],

            //'scan',
            ['attribute' => 'registerName','label' => 'Регистратор приказа', 'value' => function ($model) {
                return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'responsibilities','label' => 'Ответственные', 'contentOptions' => ['class' => 'wrap'], 'value' => function ($model) {
                $tmp = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
                $result = '';
                for ($i = 0; $i < count($tmp); $i++)
                    $result = $result.$tmp[$i]->people->secondname.' '.mb_substr($result.$tmp[$i]->people->firstname, 0, 1).'.'.mb_substr($result.$tmp[$i]->people->patronymic, 0, 1).'. ';
                return $result;
            },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
