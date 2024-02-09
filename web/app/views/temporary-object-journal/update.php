<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryObjectJournal */

$this->title = 'Update Temporary Object Journal: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Temporary Object Journals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="temporary-object-journal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
