<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\EventForm */

$this->title = 'Create Event Form';
$this->params['breadcrumbs'][] = ['label' => 'Event Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
