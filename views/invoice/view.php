<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\InvoiceWork */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Накладные / акты', 'url' => ['index']];
$this->params['breadcrumbs'][] = '№'.$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="invoice-view">

    <h1><?= Html::encode('№'.$this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить объект?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            'number',
            ['attribute' => 'contractString', 'format' => 'raw'],
            'date_product',
            'date_invoice',
            ['attribute' => 'documentLink', 'format' => 'raw'],
        ],
    ]) ?>

    <h3><u>Записи</u></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'entries', 'format' => 'raw'],
        ],
    ]) ?>

</div>
