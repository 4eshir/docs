<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\BranchWork */

$this->title = 'Добавление отдела';
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelAuditorium' => $modelAuditorium,
    ]) ?>

</div>
