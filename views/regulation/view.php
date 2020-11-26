<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Положение', 'url' => ['index']];
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
                return $order->fullName;
            }],
            'ped_council_number',
            'ped_council_date',
            'par_council_number',
            'par_council_date',
            'state',
            ['label' => 'Скан приказа', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['regulation/get-file', 'fileName' => $model->scan, 'modelId' => $model->id]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
