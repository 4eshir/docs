<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentIn */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Входящая документация';
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->session;
$tempArchive = $session->get("archiveIn");
?>
<div class="document-in-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить входящий документ', ['create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить резерв', ['document-in/create-reserve'], ['class' => 'btn btn-warning', 'style' => 'display: inline-block;']) ?>
        <?php
        if ($tempArchive === null)
            echo Html::a('Показать архивные документы', ['document-in/index', 'archive' => 1, 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block; background-color: #ededed']);
        else
            echo Html::a('Скрыть архивные документы', ['document-in/index', 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block; background-color: #ededed']);
        ?>
    </p>
    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?php

    $gridColumns = [
        ['attribute' => 'fullNumber', 'label' => '№ п/п', 'value' => function($model){
            if ($model->local_postfix == null)
                return $model->local_number;
            else
                return $model->local_number.'/'.$model->local_postfix;
        }],
        ['attribute' => 'local_date', 'label' => 'Дата поступления<br>документа', 'encodeLabel' => false],
        ['attribute' => 'real_date', 'label' => 'Дата входящего<br>документа', 'encodeLabel' => false],
        ['attribute' => 'real_number', 'label' => 'Рег. номер<br>входящего док.', 'encodeLabel' => false],

        ['attribute' => 'companyName', 'label' => 'Наименование<br>корреспондента', 'encodeLabel' => false, 'value' => function ($model) {
            return $model->company->name;
        }],
        ['attribute' => 'document_theme', 'label' => 'Тема документа', 'encodeLabel' => false],
        ['attribute' => 'sendMethodName','label' => 'Способ получения', 'value' => 'sendMethod.name'],
        ['attribute' => 'needAnswer', 'label' => 'Ответ', 'value' => function($model){
            $links = \app\models\work\InOutDocsWork::find()->where(['document_in_id' => $model->id])->one();


            if ($links == null)
                return '';
            if ($links->document_out_id == null)
            {
                if ($links->people == null)
                {
                    if ($links->date == null)
                        return 'Требуется ответ';
                    else
                        return 'До '.$links->date;
                }
                return 'До '.$links->date.' от '.$links->peopleWork->shortName;
            }

            else
                return 'Исходящий документ "'.\app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->document_theme.'"';


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
    <div style="margin-bottom: 20px">

    <?php echo '<div style="margin-bottom: 10px; margin-top: 20px">'.Html::a('Показать просроченные документы', \yii\helpers\Url::to(['document-in/index', 'sort' => '1'])).
        ' || '.Html::a('Показать документы, требующие ответа', \yii\helpers\Url::to(['document-in/index', 'sort' => '2'])).
        ' || '.Html::a('Показать все документы', \yii\helpers\Url::to(['document-in/index'])).'</div>' ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => false,
            'rowOptions' => function($data) {
                $links = \app\models\work\InOutDocsWork::find()->where(['document_in_id' => $data['id']])->one();
                if ($links == null || $links->document_out_id !== null)
                    return ['class' => 'default'];
                else if ($links->date !== null && $links->date < date("Y-m-d"))
                    return ['class' => 'danger'];
                else
                    return ['class' => 'warning'];
            },
            'columns' => [

                ['attribute' => 'fullNumber', 'label' => '№ п/п', 'value' => function($model){
                    if ($model->local_postfix == null)
                        return $model->local_number;
                    else
                        return $model->local_number.'/'.$model->local_postfix;
                }],
                ['attribute' => 'local_date', 'label' => 'Дата поступления<br>документа', 'encodeLabel' => false],
                ['attribute' => 'real_date', 'label' => 'Дата входящего<br>документа', 'encodeLabel' => false],
                ['attribute' => 'real_number', 'label' => 'Рег. номер<br>входящего док.', 'encodeLabel' => false],

                ['attribute' => 'companyName', 'label' => 'Наименование<br>корреспондента', 'encodeLabel' => false, 'value' => function ($model) {
                    return $model->company->name;
                }],
                ['attribute' => 'document_theme', 'label' => 'Тема документа', 'encodeLabel' => false],
                ['attribute' => 'sendMethodName','label' => 'Способ получения', 'value' => 'sendMethod.name'],
                ['attribute' => 'needAnswer', 'label' => 'Ответ', 'value' => function($model){
                   $links = \app\models\work\InOutDocsWork::find()->where(['document_in_id' => $model->id])->one();


                   if ($links == null)
                       return '';
                    if ($links->document_out_id == null)
                    {
                        if ($links->people == null)
                        {
                            if ($links->date == null)
                                return 'Требуется ответ';
                            else
                                return 'До '.$links->date;
                        }
                        return 'До '.$links->date.' от '.$links->peopleWork->shortName;
                    }

                    else
                        return Html::a('Исходящий документ "'.\app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->document_theme.'"',
                            \yii\helpers\Url::to(['docs-out/view', 'id' => \app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->id]));


                }, 'format' => 'raw'],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

