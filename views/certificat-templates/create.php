<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\CertificatTemplates */

$this->title = 'Создать шаблон сертификата';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны сертификатов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificat-templates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
