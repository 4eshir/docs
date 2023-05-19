<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ProductUnion */

$this->title = 'Update Product Union: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Product Unions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-union-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
