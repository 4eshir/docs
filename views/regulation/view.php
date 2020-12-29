<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */

$this->title = $model->name;
$this->title = 'Добавить положение';
$session = Yii::$app->session;
$tmp = \app\models\common\RegulationType::find()->where(['id' => $session->get('type')])->one()->name;

$this->params['breadcrumbs'][] = ['label' => $tmp, 'url' => ['index', 'c' => $session->get('type')]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="regulation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить положение?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date',
            'name',
            ['attribute' => 'order_id', 'label' => 'Приказ', 'value' => function($model){
                $order = \app\models\common\DocumentOrder::find()->where(['id' => $model->order_id])->one();
                return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
            }, 'format' => 'raw'],
            'ped_council_number',
            'ped_council_date',
            'par_council_number',
            'par_council_date',
            ['label' => 'Состояние', 'attribute' => 'state', 'value' => function($model){
                if ($model->state) return 'Актуально';
                $exp = \app\models\common\Expire::find()->where(['expire_regulation_id' => $model->order_id])->one();
                $order = \app\models\common\DocumentOrder::find()->where(['id' => $exp->active_regulation_id])->one();
                $doc_num = 0;
                if ($order->order_postfix == null)
                    $doc_num = $order->order_number.'/'.$order->order_copy_id;
                else
                    $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                return 'Утратило силу в связи с приказом '.Html::a('№'.$doc_num, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id])).'<br>';

            }, 'format' => 'raw'],
            ['label' => 'Скан положения', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['regulation/get-file', 'fileName' => $model->scan, 'modelId' => $model->id]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
