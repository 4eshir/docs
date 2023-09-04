<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\Container */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Контейнеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
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
            'name',
            ['attribute' => 'containerLink', 'format' => 'raw'],
            ['attribute' => 'objectLink', 'format' => 'raw'],
            ['attribute' => 'auditoriumLink', 'format' => 'raw'],
            ['attribute' => 'objectsInContainer', 'format' => 'raw'],
            ['attribute' => 'containersInContainer', 'format' => 'raw'],
        ],
    ]) ?>

</div>
