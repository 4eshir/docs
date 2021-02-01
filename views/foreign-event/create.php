<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEvent */

$this->title = 'Добавление внешнего мероприятия';
$this->params['breadcrumbs'][] = ['label' => 'Участие во внешних мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelParticipants' => $modelParticipants,
        'modelAchievement' => $modelAchievement
    ]) ?>

</div>
