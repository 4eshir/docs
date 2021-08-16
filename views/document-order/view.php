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
    </p>

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
            ['label' => 'Утратили силу приказы', 'attribute' => 'expires', 'value' => function($model){
                $exp = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->all();
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
                return $res;
            }, 'format' => 'raw'],
            ['label' => 'Утратили силу положения', 'attribute' => 'expires', 'value' => function($model){
                $exp = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->all();
                $res = '';
                foreach ($exp as $expOne)
                {
                    $reg = \app\models\work\RegulationWork::find()->where(['id' => $expOne->expire_regulation_id])->one();
                    if ($expOne->expire_regulation_id !== null)
                        $res = $res . Html::a('Положение '.$reg->name, \yii\helpers\Url::to(['regulation/view', 'id' => $reg->id])).'<br>';

                }
                return $res;
            }, 'format' => 'raw'],
            ['label' => 'Скан приказа', 'attribute' => 'Scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $model->scan, 'modelId' => $model->id, 'type' => 'scan']));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'docFiles', 'value' => function ($model) {
                $split = explode(" ", $model->doc);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'docs'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'По основной деятельности', 'attribute' => 'type', 'value' => function ($model) {
                return $model->type == 0 ? 'Нет' : 'Да';
            }],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['label' => 'Кто регистрировал', 'attribute' => 'register_id', 'value' => $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'. '.mb_substr($model->register->patronymic, 0, 1).'.'],
        ],
    ]) ?>

    <div <?php echo $model->type == 0 ? '' : 'hidden'; ?>>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['attribute' => 'groupsLink', 'format' => 'raw'],
            ],
        ]) ?>
    </div>

</div>
