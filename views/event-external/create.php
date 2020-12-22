<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\EventExternal */

$this->title = 'Create Event External';
$this->params['breadcrumbs'][] = ['label' => 'Event Externals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-external-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
