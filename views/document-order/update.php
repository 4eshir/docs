<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */

$this->title = 'Редактировать приказ: ' . $model->order_name;
$this->params['breadcrumbs'][] = ['label' => 'Приказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="document-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $fioDb = \app\models\common\People::find()->where(['id' => $model->signed_id])->one();
    $model->signedString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    $fioDb = \app\models\common\People::find()->where(['id' => $model->executor_id])->one();
    $model->executorString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    $fioDb = \app\models\common\People::find()->where(['id' => $model->register_id])->one();
    $model->registerString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    $fioDb = \app\models\common\People::find()->where(['id' => $model->bring_id])->one();
    $model->bringString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    ?>

    <?= $this->render('_form', [
        'model' => $model,
        'modelResponsible' => $modelResponsible,
    ]) ?>

</div>
