<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\temporary\TClick */

$this->title = 'Create T Click';
$this->params['breadcrumbs'][] = ['label' => 'T Clicks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tclick-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
