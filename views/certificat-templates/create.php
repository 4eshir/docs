<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\CertificatTemplates */

$this->title = 'Create Certificat Templates';
$this->params['breadcrumbs'][] = ['label' => 'Certificat Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificat-templates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
