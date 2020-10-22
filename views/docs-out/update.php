<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */

$this->title = 'Редактирование исходящего документа: ' . $model->document_name;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="document-out-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
