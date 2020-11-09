<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentIn */

$this->title = $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="document-in-view">

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
            ['label' => 'Локальный номер', 'attribute' => 'local_number'],
            ['label' => 'Локальная дата', 'attribute' => 'local_date'],
            ['label' => 'Исходящий номер', 'attribute' => 'real_number'],
            ['label' => 'Исходящая дата', 'attribute' => 'real_date'],
            ['label' => 'Должность корреспондента', 'attribute' => 'position_id', 'value' => function($model){
                if ($model->position_id == 7)
                    return '';
                return $model->position->name;
            }],
            ['label' => 'Компания корреспондента', 'attribute' => 'company_id', 'value' => $model->company->name],
            ['label' => 'Тема документа', 'attribute' => 'document_theme'],
            ['label' => 'Кем подписан', 'attribute' => 'signed_id', 'value' => $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'. '.mb_substr($model->signed->patronymic, 0, 1).'.'],
            ['label' => 'Кому адресован', 'attribute' => 'document_theme'],
            ['label' => 'Кто принял', 'attribute' => 'get_id', 'value' => $model->get->secondname.' '.mb_substr($model->get->firstname, 0, 1).'. '.mb_substr($model->get->patronymic, 0, 1).'.'],
            ['label' => 'Способ получения', 'attribute' => 'send_method_id', 'value' => $model->sendMethod->name],
            ['label' => 'Скан документа', 'attribute' => 'scan'],
            ['label' => 'Приложения', 'attribute' => 'applications'],
            ['label' => 'Регистратор документа', 'attribute' => 'register_id', 'value' => $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1).'.'],
        ],
    ]) ?>

</div>
