<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\UserWork */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>


<div>
    <?= $this->render('menu') ?>

    <div class="content-container col-xs-8">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'secondname',
                'firstname',
                'patronymic',
                'username',
            ],
        ]) ?>
    </div>
</div>

