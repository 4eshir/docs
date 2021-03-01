<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\TrainingGroup */

$this->title = 'Create Training Group';
$this->params['breadcrumbs'][] = ['label' => 'Training Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
        'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
        'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
    ]) ?>

</div>
