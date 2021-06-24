<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\ResponsibilityTypeWork */

$this->title = 'Редактировать вид ответственности: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Виды ответственности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="responsibility-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
