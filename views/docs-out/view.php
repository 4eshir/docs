<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOutWork */

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
            ['label' => 'Номер документа', 'attribute' => 'document_number', 'value' => function($model){
                if ($model->document_postfix == null)
                    return $model->document_number;
                else
                    return $model->document_number.'/'.$model->document_postfix;
            }],
            ['label' => 'Дата документа', 'attribute' => 'document_date'],
            ['label' => 'Тема документа', 'attribute' => 'document_theme'],
            ['label' => 'ФИО корреспондента', 'attribute' => 'correspondent_id', 'value' => $model->correspondent->secondname.' '.mb_substr($model->correspondent->firstname, 0, 1).'. '.mb_substr($model->correspondent->patronymic, 0, 1).'.'],
            ['label' => 'Должность корреспондента', 'attribute' => 'position_id', 'value' => function($model){
                if ($model->position_id == 7)
                    return '';
                return $model->position->name;
            }],
            ['label' => 'Компания корреспондента', 'attribute' => 'company_id', 'value' => $model->company->name],
            ['label' => 'Кем подписан', 'attribute' => 'signed_id', 'value' => $model->signed->secondname.' '.mb_substr($model->signed->firstname, 0, 1).'. '.mb_substr($model->signed->patronymic, 0, 1).'.'],
            ['label' => 'Кто исполнил', 'attribute' => 'executor_id', 'value' => $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1).'.'],
            ['label' => 'Метод отправки', 'attribute' => 'send_method_id', 'value' => $model->sendMethod->name],
            ['label' => 'Дата отправления', 'attribute' => 'sent_date'],
            ['attribute' => 'isAnswer', 'label' => 'Является ответом на', 'value' => function($model){
                $links = \app\models\work\InOutDocsWork::find()->where(['document_out_id' => $model->id])->one();
                if ($links == null)
                    return '';
                else
                    return Html::a('Входящий документ "'.\app\models\work\DocumentInWork::find()->where(['id' => $links->document_in_id])->one()->document_theme.'"',
                        \yii\helpers\Url::to(['document-in/view', 'id' => \app\models\work\DocumentInWork::find()->where(['id' => $links->document_in_id])->one()->id]));
            }, 'format' => 'raw'],
            ['label' => 'Скан документа', 'attribute' => 'Scan', 'value' => function ($model) {
                return Html::a($model->Scan, \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $model->Scan, 'type' => 'scan']));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'docFiles', 'value' => function ($model) {
                $split = explode(" ", $model->doc);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'docs'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applicationFiles', 'value' => function ($model) {
                $split = explode(" ", $model->applications);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'apps'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => $model->creatorWork->secondname.' '.mb_substr($model->creatorWork->firstname, 0, 1).'. '.mb_substr($model->creatorWork->patronymic, 0, 1).'.'],
            ['label' => 'Последний редактор', 'attribute' => 'last_edit_id', 'value' => $model->lastEditWork->secondname.' '.mb_substr($model->lastEditWork->firstname, 0, 1).'. '.mb_substr($model->lastEditWork->patronymic, 0, 1).'.'],
        ],
    ]) ?>

</div>
