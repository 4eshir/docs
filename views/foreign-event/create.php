<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEvent */

$this->title = 'Create Foreign Event';
$this->params['breadcrumbs'][] = ['label' => 'Foreign Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelParticipants' => $modelParticipants
    ]) ?>

</div>
