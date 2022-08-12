<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\Certificat */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Certificats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="certificat-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
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
            ['attribute' => 'certificat_number', 'label' => 'Номер сертификата', 'format' => 'raw'],
            ['attribute' => 'certificat_template_id', 'label' => 'Шаблон сертификата', 'format' => 'raw'],
            ['attribute' => 'training_group_participant_id', 'label' => 'Учащийся', 'format' => 'raw'],
        ],
    ]) ?>

</div>
