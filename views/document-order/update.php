<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */

$this->title = 'Update Document Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Document Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="document-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelResponsible' => $modelResponsible,
    ]) ?>

</div>
