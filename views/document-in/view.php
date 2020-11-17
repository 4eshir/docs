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
            ['label' => '№ п/п', 'attribute' => 'id', 'value' => function($model){
                if ($model->local_postfix == null)
                    return $model->local_number;
                else
                    return $model->local_number.'/'.$model->local_postfix;
            }],
            ['label' => 'Дата поступления документа', 'attribute' => 'local_date'],
            ['label' => 'Дата входящего документа', 'attribute' => 'real_date'],
            ['label' => 'Регистрационный номер входящего документа', 'attribute' => 'real_number'],
            ['label' => 'ФИО корреспондента', 'attribute' => 'correspondent_id', 'value' => $model->correspondent->secondname.' '.mb_substr($model->correspondent->firstname, 0, 1).'. '.mb_substr($model->correspondent->patronymic, 0, 1).'.'],
            ['label' => 'Должность корреспондента', 'attribute' => 'position_id', 'value' => function($model){
                if ($model->position_id == 7)
                    return '';
                return $model->position->name;
            }],
            ['label' => 'Организация корреспондента', 'attribute' => 'company_id', 'value' => $model->company->name],
            ['label' => 'Тема документа', 'attribute' => 'document_theme'],
            ['label' => 'Способ получения', 'attribute' => 'send_method_id', 'value' => $model->sendMethod->name],
            ['label' => 'Скан приказа', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['document-in/get-file', 'fileName' => $model->scan, 'modelId' => $model->id]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applications'],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['label' => 'Регистратор документа', 'attribute' => 'register_id', 'value' => $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1).'.'],
        ],
    ]) ?>

</div>
