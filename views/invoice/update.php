<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Invoice */

$this->title = 'Редактировать документ: №' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Первичные документы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' =>  '№' . $model->number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelObjects' => $modelObjects,
    ]) ?>

</div>
