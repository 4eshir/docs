<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\CertificatTemplates */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны сертификатов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="certificat-templates-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить шаблон сертификата?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            ['attribute' => 'path', 'value' => Html::a($model->path, \yii\helpers\Url::to(['certificat-templates/get-file', 'fileName' => $model->path, 'modelId' => $model->id])), 'format' => 'raw'],
            //['attribute' => 'path', 'value' => '<img src="'.Yii::$app->basePath . '/upload/files/certificat_templates/' . $model->path . '" style="width: 100px; height: 300px;">', 'format' => 'raw'],

        ],
    ]) ?>
    
</div>
