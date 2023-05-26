<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\temporary\TClick */

$this->title = 'Update T Click: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'T Clicks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tclick-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
