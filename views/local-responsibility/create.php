<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */

$this->title = 'Добавление новой ответственности работника';
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="local-responsibility-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
