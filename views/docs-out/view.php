<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->document_theme;
\yii\web\YiiAsset::register($this);
?>
<div class="document-out-view">

    <h1><?= Html::encode($model->document_theme) ?></h1>

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
            ['label' => 'Номер документа', 'attribute' => 'document_number'],
            ['label' => 'Дата документа', 'attribute' => 'document_date'],
            ['label' => 'Тема документа', 'attribute' => 'document_theme'],
            ['label' => 'Должность корреспондента', 'attribute' => 'position_id', 'value' => $model->position->name],
            ['label' => 'Компания корреспондента', 'attribute' => 'company_id', 'value' => $model->company->name],
            ['label' => 'Кем подписан', 'attribute' => 'signed_id', 'value' => $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'. '.mb_substr($model->signed->patronymic, 0, 1).'.'],
            ['label' => 'Кто исполнил', 'attribute' => 'executor_id', 'value' => $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1).'.'],
            ['label' => 'Метод отправки', 'attribute' => 'send_method_id', 'value' => $model->sendMethod->name],
            ['label' => 'Дата отправления', 'attribute' => 'sent_date'],
            ['label' => 'Скан документа', 'attribute' => 'Scan', 'value' => function ($model) {
                return Html::a($model->Scan, \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $model->Scan]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applicationFiles', 'value' => function ($model) {
                $split = explode(" ", $model->applications);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $split[$i]])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Кто зарегистрировал', 'attribute' => 'register_id', 'value' => $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1).'.'],
        ],
    ]) ?>

</div>
