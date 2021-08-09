<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\UserWork */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <div class="content-container" style="float: left">
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
<div style="width:100%; height:1px; clear:both;"></div>