<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\PeopleWork */

$this->title = 'Добавить человека';
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
    ]) ?>

</div>
