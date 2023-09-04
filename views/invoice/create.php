<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Invoice */

$this->title = 'Создать документ о поступлении материальных объектов';
$this->params['breadcrumbs'][] = ['label' => 'Документы о поступлении', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelObjects' => $modelObjects,
    ]) ?>

</div>
