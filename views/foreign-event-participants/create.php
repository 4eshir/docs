<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEventParticipants */

$this->title = 'Добавление нового участника образовательной деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Участники образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
