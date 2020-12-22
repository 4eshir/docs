<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\EventExternal */

$this->title = 'Update Event External: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Externals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-external-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
