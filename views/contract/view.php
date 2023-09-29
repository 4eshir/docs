<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\ContractWork */

$this->title = 'Договор № ' . $model->number . ' от ' . $model->date;
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contract-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную организацию?',
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
            ['label' => 'Дата договора', 'attribute' => 'date'],
            ['label' => 'Номер договора', 'attribute' => 'number'],
            ['label' => 'Категории материальных объектов в договоре', 'attribute' => 'categoriesString', 'format' => 'raw'],
            ['attribute' => 'contractorLink', 'format' => 'raw'],
            ['attribute' => 'file', 'value' => function ($model) {
                return Html::a($model->file, \yii\helpers\Url::to(['contract/get-file', 'fileName' => $model->file, 'modelId' => $model->id]));
            }, 'format' => 'raw'],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
        ],
    ]) ?>

    <br><h3><u>Документы о поступлении</u></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'invoices', 'format' => 'raw'],
        ],
    ]) ?>
</div>
