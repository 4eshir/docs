<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\TeacherParticipantWork */

$this->title = 'Create Teacher Participant';
$this->params['breadcrumbs'][] = ['label' => 'Teacher Participants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-participant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
