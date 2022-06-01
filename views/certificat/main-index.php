<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Certificat */

$this->title = 'Генерация сертификатов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificat-create">

    <?php
        echo Html::a('Генератор сертификатов', \yii\helpers\Url::to(['certificat/index']));
        echo '<br>';
        echo Html::a('База шаблонов сертификатов', \yii\helpers\Url::to(['certificat-templates/index']));
    ?>
</div>
