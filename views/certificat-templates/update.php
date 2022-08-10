<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\CertificatTemplates */

$this->title = 'Редактировать шаблон сертификата: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны сертификатов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="certificat-templates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
