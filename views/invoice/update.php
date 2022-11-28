<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Invoice */

$this->title = 'Редактировать документ: №' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Документ о поступлении', 'url' => ['index']];
$type = $model->type;
$name = ['Накладная', 'Акт', 'УПД', 'Протокол'];
$this->params['breadcrumbs'][] = ['label' =>  $name[$type] . ' №' . $model->number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelObjects' => $modelObjects,
    ]) ?>

</div>
