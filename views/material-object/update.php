<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\MaterialObjectWork */

$this->title = 'Update Material Object: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Material Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="material-object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
