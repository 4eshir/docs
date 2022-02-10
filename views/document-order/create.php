<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOrderWork */
$session = Yii::$app->session;
$this->title = 'Добавить приказ';
$this->params['breadcrumbs'][] = ['label' => 'Приказы', 'url' => ['index', 'c' => $session->get('type') == 1 ? 1 : 0]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-order-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
        'modelResponsible' => $modelResponsible,
        'modelExpire' => $modelExpire,
    ]) ?>

</div>
