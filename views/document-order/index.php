<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDocumentOrder */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>

<?php
$session = Yii::$app->session;

$this->title = $session->get('type') == 1 || $session->get('type') == 10 ? 'Приказы по основной деятельности' : 'Приказы по образовательной деятельности';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-secondary {
        color: #fff;
        background-color: #808080;
        border-color: #696969;
    }

    .btn-secondary:focus, .btn-secondary:hover {
        color: #fff;
        background-color: #606060;
        border-color: #4F4F4F;
    }

    .btn {
        margin-right: 5px;
    }
</style>

<div style="margin-left: auto; margin-right: 0; width: max-content;">
    <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px;  margin-left: 10px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Серый цвет - приказ утратил силу или был изменен другим приказом&#10Желтый цвет - архивный приказ&#10Красный цвет - в приказе есть ошибки">❔</div>
</div>

<div class="document-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
            if ($session->get('type') == 1)
            {
               echo Html::a('Добавить приказ по основной деятельности', ['create', 'modelType' => '1'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']);
               echo Html::a('Добавить образовательный приказ', ['create', 'modelType' => '0'], ['class' => 'btn btn-warning', 'style' => 'display: inline-block;']);
               echo Html::a('Добавить приказ об участии', ['create', 'modelType' => '2'], ['class' => 'btn btn-info', 'style' => 'display: inline-block;']);
               echo Html::a('Добавить резерв', ['create-reserve'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block;']);
            }
            else
            {
                echo Html::a('Добавить образовательный приказ', ['create', 'modelType' => '0'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']);
            }
        ?>
    </p>
    <?php
        echo $this->render('_search', ['model' => $searchModel])
    ?>
    <?php

    $gridColumns = [
        ['attribute' => 'order_date', 'label' => 'Дата приказа'],
        ['attribute' => 'documentNumberString', 'label' => 'Номер приказа'],
        ['attribute' => 'order_name', 'label' => 'Наименование приказа'],
        ['attribute' => 'bringName', 'label' => 'Проект вносит', 'value' => function($model)
        {
            return $model->bringWork->secondname.' '.mb_substr($model->bringWork->firstname, 0, 1).'. '.mb_substr($model->bringWork->patronymic, 0, 1);
        }],
        ['attribute' => 'executorName', 'label' => 'Исполнитель', 'value' => function($model)
        {
            return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1);
        }],
        ['attribute' => 'responsiblies', 'label' => 'Ответственные', 'value' => function($model)
        {
            $resp = \app\models\work\ResponsibleWork::find()->where(['document_order_id' => $model->id])->all();
            $result = '';
            foreach ($resp as $respOne)
                $result = $result.$respOne->people->secondname.' '.mb_substr($respOne->people->firstname, 0, 1).'. '.mb_substr($respOne->people->patronymic, 0, 1).'. ';
            return $result;
        }],
        ['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
            if ($model->state == 1)
                return 'Актуален';
            else
                return 'Утратил силу';
        }],
        ['attribute' => 'creatorName', 'label' => 'Регистратор приказа', 'value' => function($model)
        {
            return $model->creatorWork->secondname.' '.mb_substr($model->creatorWork->firstname, 0, 1).'. '.mb_substr($model->creatorWork->patronymic, 0, 1);
        }],
        ['attribute' => 'key_words', 'label' => 'Ключевые слова', 'value' => function($model)
        {
            return $model->key_words;
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
        'rowOptions' => function($data) {
            if (strtotime($data['order_date']) < strtotime('2021-01-01'))
                return ['class' => 'warning'];
            else if ($data['state'] == 0)
                return ['style' => 'background: #c0c0c0'];
            //else if ($data['changeDocs'] == '')
            //    return ['class' => 'warning'];
            else if ($data['errorsWork'] !== '')
                return ['class' => 'danger'];
            else if ($data['changeDocs'] != '')
                return ['class' => 'warning'];
            else
                return ['class' => 'default'];

        },
        'summary' => false,
        'columns' => [
            ['attribute' => 'order_date', 'label' => 'Дата приказа'],
            ['attribute' => 'documentNumberString', 'label' => 'Номер приказа'],
            ['attribute' => 'order_name', 'label' => 'Наименование приказа'],
            ['attribute' => 'bringName','label' => 'Проект вносит', 'value' => function ($model) {
                return $model->bringWork->secondname.' '.mb_substr($model->bringWork->firstname, 0, 1).'.'.mb_substr($model->bringWork->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'executorName','label' => 'Исполнитель', 'value' => function ($model) {
                if ($model->executor)
                    return $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'.'.mb_substr($model->executor->patronymic, 0, 1).'.';
                return '';
            },
            ],

            ['attribute' => 'responsibilities','label' => 'Ответственные', 'contentOptions' => ['encode' => 'false'], 'value' => function ($model) {
                $tmp = \app\models\work\ResponsibleWork::find()->where(['document_order_id' => $model->id])->all();
                $result = '';
                for ($i = 0; $i < count($tmp); $i++)
                    $result = $result.$tmp[$i]->people->secondname.' '.mb_substr($tmp[$i]->people->firstname, 0, 1).'.'.mb_substr($tmp[$i]->people->patronymic, 0, 1).'. <br>';

                return $result;
            }, 'format' => 'html'
            ],
            /*['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
                if ($model->state == 1)
                    return 'Актуален';
                else
                {
                    $exp = \app\models\work\ExpireWork::find()->where(['expire_order_id' => $model->id])->one();
                    $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $exp->active_regulation_id])->one();
                    $doc_num = 0;
                    if ($order->order_postfix == null)
                        $doc_num = $order->order_number.'/'.$order->order_copy_id;
                    else
                        $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                    return 'Утратил силу в связи с приказом '.Html::a('№'.$doc_num, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
                }
            }, 'format' => 'raw'],*/
            ['attribute' => 'state', 'label' => 'Состояние', 'format' => 'raw', 'value' => 'stateAndColor'],
            ['attribute' => 'key_words', 'label' => 'Ключевые слова', 'visible' => $session->get('type') == 1 ? false : true],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
