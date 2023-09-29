<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentInWork */

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
            ['label' => 'Скан документа', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, ['get-file', 'fileName' => $model->scan, 'modelId' => $model->id, 'type' => 'scan'],
                    ['data-toggle' => "modal", 'data-target' => "#exampleModal"]);
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'docFiles', 'value' => function ($model) {
                $split = explode(" ", $model->doc);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['document-in/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'docs'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applications', 'value' => function ($model) {
                $split = explode(" ", $model->applications);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['document-in/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'apps'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['attribute' => 'needAnswer', 'label' => 'Ответ', 'value' => function($model){
                $links = \app\models\work\InOutDocsWork::find()->where(['document_in_id' => $model->id])->one();
                if ($links == null)
                    return '';
                if ($links->document_out_id == null)
                    return 'Требуется ответ';
                else
                    return Html::a('Исходящий документ "'.\app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->document_theme.'"',
                        \yii\helpers\Url::to(['docs-out/view', 'id' => \app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->id]));
            }, 'format' => 'raw'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => $model->creatorWork->secondname.' '.mb_substr($model->creatorWork->firstname, 0, 1).'. '.mb_substr($model->creatorWork->patronymic, 0, 1).'.'],
            ['label' => 'Последний редактор', 'attribute' => 'last_edit_id', 'value' => $model->lastEditWork->secondname.' '.mb_substr($model->lastEditWork->firstname, 0, 1).'. '.mb_substr($model->lastEditWork->patronymic, 0, 1).'.'],
        ],
    ]) ?>

</div>
