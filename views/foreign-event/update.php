<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEvent */

$this->title = 'Редактировать внешнее мероприятие: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Участие во внешних мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="foreign-event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelParticipants' => $modelParticipants,
        'modelAchievement' => $modelAchievement,
    ]) ?>

</div>
