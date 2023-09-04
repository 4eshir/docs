<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ProductUnion */

$this->title = 'Create Product Union';
$this->params['breadcrumbs'][] = ['label' => 'Product Unions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-union-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
