<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOrder */
$session = Yii::$app->session;
$this->title = 'Редактировать приказ: ' . $model->order_name;
$this->params['breadcrumbs'][] = ['label' => 'Приказы', 'url' => ['index', 'c' => $session->get('type') == 1 ? 1 : 0]];
$this->params['breadcrumbs'][] = ['label' => $model->order_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="document-order-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

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
        'modelExpire' => $modelExpire,
    ]) ?>

</div>
