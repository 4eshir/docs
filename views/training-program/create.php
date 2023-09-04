<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingProgramWork */

$this->title = 'Добавить образовательную программу';
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-program-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelAuthor' => $modelAuthor,
        'modelThematicPlan' => $modelThematicPlan,
    ]) ?>

</div>
