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

    <?php
        if ($modelType == 1) // по основной деятельности
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
        else if ($modelType == 0)   // по образовательной деятельности
        {
            echo $this->render('education', [
                'model' => $model,
                'modelResponsible' => $modelResponsible,
                'modelExpire' => $modelExpire,
            ]);
        }
        else
            var_dump('Произошла ошибка. Вернитесь на главную страницу и попробуйте снова');

    ?>
</div>
