<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Document Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="document-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Номер приказа', 'attribute' => 'order_number', 'value' => $model->order_number],
            ['label' => 'Наименование приказа', 'attribute' => 'order_name', 'value' => $model->order_name],
            ['label' => 'Дата приказа', 'attribute' => 'order_date', 'value' => $model->order_date],
            ['label' => 'Кем подписан', 'attribute' => 'signed_id', 'value' => $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'. '.mb_substr($model->signed->patronymic, 0, 1).'.'],
            ['label' => 'Проект вносит', 'attribute' => 'bring_id', 'value' => $model->bring->secondname.' '.mb_substr($model->bring->firstname, 0, 1).'. '.mb_substr($model->bring->patronymic, 0, 1).'.'],
            ['label' => 'Исполнитель', 'attribute' => 'executor_id', 'value' => $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1).'.'],
            ['label' => 'Отетственные по приказу', 'value' => function ($model) {
                $res = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
                $html = '';
                for ($i = 0; $i != count($res); $i++)
                    $html = $html.$res[$i]->people->secondname.' '.mb_substr($res[$i]->people->firstname, 0, 1).'. '.mb_substr($res[$i]->people->patronymic, 0, 1).'.<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Скан приказа', 'attribute' => 'Scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $model->scan]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Кто регистрировал', 'attribute' => 'register_id', 'value' => $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1).'.'],
        ],
    ]) ?>

</div>
