<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\KindObject */

$this->title = 'Update Kind Object: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kind Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="kind-object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelCharacteristics' => $modelCharacteristics,
    ]) ?>

</div>
