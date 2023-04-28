<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\InvoiceWork */

$type = $model->type;
$name = ['Накладная', 'Акт', 'УПД', 'Протокол'];
$this->title = $name[$type] . ' №' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Документы о поступлении', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' =>  $name[$type] . ' №' . $model->number];
\yii\web\YiiAsset::register($this);
?>
<div class="invoice-view">

    <h1><?= $this->title ?></h1>

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
            'date_invoice',
            'number',
            ['attribute' => 'contractorString', 'format' => 'raw'],
            ['attribute' => 'contractString', 'format' => 'raw'],
            'date_product',
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
