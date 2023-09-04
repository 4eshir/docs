<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\ContractWork */

$this->title = 'Редактировать договор: №' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Договор № ' . $model->number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="contract-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
