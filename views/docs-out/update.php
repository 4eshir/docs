<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOutWork */

$this->title = 'Редактирование исходящего документа: ' . $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_theme, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="document-out-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?php
        $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->signed_id])->one();
        $model->signedString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

        $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->executor_id])->one();
        $model->executorString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

        $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->creator_id])->one();
        $model->creatorString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
