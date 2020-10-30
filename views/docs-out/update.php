<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */

$this->title = 'Редактирование исходящего документа: ' . $model->document_number . ' ' . $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_theme, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="document-out-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
        $fioDb = \app\models\common\People::find()->where(['id' => $model->signed_id])->one();
        $model->signedString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

        $fioDb = \app\models\common\People::find()->where(['id' => $model->executor_id])->one();
        $model->executorString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

        $fioDb = \app\models\common\People::find()->where(['id' => $model->register_id])->one();
        $model->registerString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
