<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\PeopleMaterialObjectWork */

$this->title = 'Update People Material Object: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'People Material Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="people-material-object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
