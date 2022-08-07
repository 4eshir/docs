<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupWork */

$this->title = 'Добавить учебную группу';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
        'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
        'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
        'modelOrderGroup' => $modelOrderGroup,
        'modelTeachers' => $modelTeachers,
        'modelProjectThemes' => $modelProjectThemes,
        'modelExperts' => $modelExperts,
    ]) ?>

</div>
