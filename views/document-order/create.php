<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */

$this->title = 'Create Document Order';
$this->params['breadcrumbs'][] = ['label' => 'Document Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
