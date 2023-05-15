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
        <?php
        $error = $model->getErrorsWork();
        if ($error !== '' && (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)))
            echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить все ошибки?',
                    'method' => 'post',
                ],]);
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
            'date_invoice',
            'number',
            ['attribute' => 'contractorLink', 'format' => 'raw'],
            ['attribute' => 'contractLink', 'format' => 'raw'],
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
