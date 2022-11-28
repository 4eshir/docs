<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\CertificatWork */

$this->title = 'Сертификат № '. $model->getCertificatLongNumber();//$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="certificat-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) // редактирование не доступно?>
        <?php   // удаление доступно только суперу или админу
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6) || \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7))
            echo Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить сертификат?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            ['attribute' => 'certificatLongNumber', 'format' => 'raw'],
            ['attribute' => 'certificatTemplateName', 'format' => 'raw'],
            ['attribute' => 'participantName', 'format' => 'raw'],
            ['attribute' => 'participantGroup', 'format' => 'raw'],
            ['attribute' => 'pdfFile', 'format' => 'raw'],
        ],
    ]) ?>

</div>
