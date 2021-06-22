<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */

$this->title = 'Create Material Object';
$this->params['breadcrumbs'][] = ['label' => 'Material Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-object-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
