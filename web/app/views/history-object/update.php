<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\HistoryObject */

$this->title = 'Update History Object: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'History Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="history-object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
