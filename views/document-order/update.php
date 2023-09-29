<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOrderWork */
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
    $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->signed_id])->one();
    $model->signedString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->executor_id])->one();
    $model->executorString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->creator_id])->one();
    $model->creatorString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    $fioDb = \app\models\work\PeopleWork::find()->where(['id' => $model->bring_id])->one();
    $model->bringString = $fioDb->secondname.' '.$fioDb->firstname.' '.$fioDb->patronymic;

    ?>

    <?php /*$this->render('_form', [
        'model' => $model,
        'modelResponsible' => $modelResponsible,
        'modelExpire' => $modelExpire,
    ])*/ ?>

    <?php
        if ($modelType == 1 || $modelType == 10) // по основной деятельности
        {
            echo $this->render('main-order', [
                'model' => $model,
                'modelResponsible' => $modelResponsible,
                'modelExpire' => $modelExpire,
            ]);
        }
        else if ($modelType == 2)   // об участии в мероприятии
        {
            echo $this->render('foreign-event', [
                'model' => $model,
                'modelResponsible' => $modelResponsible,
                'modelParticipants' => $modelParticipants,
            ]);
        }
        else if ($modelType == 0 || $modelType == 11)   // по образовательной деятельности
        {
            echo $this->render('education', [
                'model' => $model,
                'modelResponsible' => $modelResponsible,
                'modelExpire' => $modelExpire,
            ]);
        }
    ?>

</div>
