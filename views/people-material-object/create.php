<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\PeopleMaterialObjectWork */

$this->title = 'Create People Material Object';
$this->params['breadcrumbs'][] = ['label' => 'People Material Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-material-object-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
