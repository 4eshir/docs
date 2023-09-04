<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */

$this->title = 'Добавить объект';
$this->params['breadcrumbs'][] = ['label' => 'Материальные ценности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-object-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
