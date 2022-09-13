<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Certificat */

$this->title = 'Генерация сертификатов';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>Генерация сертификатов</h3>

<div class="certificat-create" style="margin-top: 30px;">

    <?php
        echo Html::a('Сертификаты и их создание', \yii\helpers\Url::to(['certificat/index']), ['class'=>'btn btn-success']);
        echo '<div style="padding-top: 7px"></div>';
        echo Html::a('База шаблонов сертификатов', \yii\helpers\Url::to(['certificat-templates/index']), ['class'=>'btn btn-success']);
    ?>
</div>
