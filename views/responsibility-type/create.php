<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\ResponsibilityTypeWork */

$this->title = 'Добавить вид ответственности';
$this->params['breadcrumbs'][] = ['label' => 'Вид ответственности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="responsibility-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
