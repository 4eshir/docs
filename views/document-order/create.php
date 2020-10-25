<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */

$this->title = 'Добавить приказ';
$this->params['breadcrumbs'][] = ['label' => 'Приказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelResponsible' => $modelResponsible,
    ]) ?>

</div>
