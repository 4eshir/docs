<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOrderWork */

$this->title = $model->order_name;
$session = Yii::$app->session;
$this->params['breadcrumbs'][] = ['label' => 'Приказы', 'url' => ['index', 'c' => $session->get('type') == 1 ? 1 : 0]];
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
                'confirm' => 'Вы действительно хотите удалить приказ?',
                'method' => 'post',
            ],
        ]) ?>
        <?php
        $error = $model->getErrorsWork();
        if ($error !== '' && (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)))
                //(\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 24)) || (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 32))))
            echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить в приказе все ошибки?',
                    'method' => 'post',
                ],]);
        ?>
        <?php
        if (($model->type == 0 || $model->type == 11) && $model->study_type == 0)
        {
            \yii\bootstrap\Modal::begin([
                'header' => '<p style="text-align: left; font-weight: 700; color: #f0ad4e; font-size: 1.5em;">Протоколы</p>',
                'toggleButton' => ['label' => 'Протоколы', 'class' => 'btn btn-success', 'style' => 'float: right;'],
            ]);
            echo Html::a("Шаблон протокола аттестационной комиссии", \yii\helpers\Url::to(['document-order/generation-protocol', 'order_id' => $model->id]), ['class' => 'btn btn-success']);
            echo '<br><br>';
            \yii\bootstrap\Modal::end();
        }
        ?>
    </p>

    <div class="content-container" style="color: #ff0000; font: 18px bold;">
        <?php
        $error = $model->getErrorsWork();
        if ($error != '')
        {
            echo '<p style="">';
            echo $error;
            echo '</p>';
        }
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Номер приказа', 'attribute' => 'order_number', 'value' => function($model){
                if ($model->order_postfix == null)
                    return $model->order_number.'/'.$model->order_copy_id;
                else
                    return $model->order_number.'/'.$model->order_copy_id.'/'.$model->order_postfix;
            }],
            ['label' => 'Наименование приказа', 'attribute' => 'order_name', 'value' => $model->order_name],
            ['label' => 'Дата приказа', 'attribute' => 'order_date', 'value' => $model->order_date],
            ['label' => 'Проект вносит', 'attribute' => 'bring_id', 'value' => $model->bring->secondname.' '.mb_substr($model->bring->firstname, 0, 1).'. '.mb_substr($model->bring->patronymic, 0, 1).'.'],
            ['label' => 'Исполнитель', 'attribute' => 'executor_id', 'value' => $model->executor->secondname.' '.mb_substr($model->executor->firstname, 0, 1).'. '.mb_substr($model->executor->patronymic, 0, 1).'.'],
            ['label' => 'Положения по приказу', 'value' => function ($model) {
                $res = \app\models\work\RegulationWork::find()->where(['order_id' => $model->id])->all();
                $html = '';
                for ($i = 0; $i != count($res); $i++)
                    $html = $html.Html::a('Положение "'.$res[$i]->name.'"', \yii\helpers\Url::to(['regulation/view', 'id' => $res[$i]->id])).'<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Ответственные по приказу', 'value' => function ($model) {
                $res = \app\models\work\ResponsibleWork::find()->where(['document_order_id' => $model->id])->all();
                $html = '';
                for ($i = 0; $i != count($res); $i++)
                    $html = $html.$res[$i]->people->secondname.' '.mb_substr($res[$i]->people->firstname, 0, 1).'. '.mb_substr($res[$i]->people->patronymic, 0, 1).'.<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Утратили силу документы', 'attribute' => 'expires', 'value' => function($model){
                $exp = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->andWhere(['expire_type' => 1])->all();
                $res = '';
                foreach ($exp as $expOne)
                {
                    $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $expOne->expire_order_id])->one();
                    $doc_num = 0;
                    if ($order->order_postfix == null)
                        $doc_num = $order->order_number.'/'.$order->order_copy_id;
                    else
                        $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                    if ($expOne->expire_order_id !== null)
                        $res = $res . Html::a('Приказ №'.$doc_num, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id])).'<br>';

                }

                $exp = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->andWhere(['expire_type' => 1])->all();
                foreach ($exp as $expOne)
                {
                    $reg = \app\models\work\RegulationWork::find()->where(['id' => $expOne->expire_regulation_id])->one();
                    if ($expOne->expire_regulation_id !== null)
                        $res = $res . Html::a('Положение '.$reg->name, \yii\helpers\Url::to(['regulation/view', 'id' => $reg->id])).'<br>';

                }

                return $res;
            }, 'format' => 'raw'],
            ['label' => 'Внесены изменения в документы', 'attribute' => 'expireOrders2', 'format' => 'raw'],
            ['label' => 'Был изменен документами', 'attribute' => 'changeDocs', 'format' => 'raw'],
            ['label' => 'Скан приказа', 'attribute' => 'Scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $model->scan, 'modelId' => $model->id, 'type' => 'scan']));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'changeDocFile', 'format' => 'raw'],
            ['label' => 'По основной деятельности', 'attribute' => 'type', 'value' => function ($model) {
                return $model->type == 0 ? 'Нет' : 'Да';
            }],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => $model->creatorWork->secondname.' '.mb_substr($model->creatorWork->firstname, 0, 1).'. '.mb_substr($model->creatorWork->patronymic, 0, 1).'.'],
            ['label' => 'Последний редактор', 'attribute' => 'last_edit_id', 'value' => $model->lastEditWork->secondname.' '.mb_substr($model->lastEditWork->firstname, 0, 1).'. '.mb_substr($model->lastEditWork->patronymic, 0, 1).'.'],
        ],
    ]) ?>

    <div <?php echo $model->type == 0 || $model->type == 11 ? '' : 'hidden'; ?>>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['label' => 'Группы в приказе', 'attribute' => 'groupsLink', 'format' => 'raw'],
                ['label' => 'Учащиеся в приказе', 'attribute' => 'participantsLink', 'format' => 'raw'],
            ],
        ]) ?>
    </div>

    <div <?php echo $model->type == 2 ? '' : 'hidden'; ?>>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['label' => 'Учет достижений', 'attribute' => 'foreignEventLink', 'format' => 'raw'],
            ],
        ]) ?>
    </div>

</div>
