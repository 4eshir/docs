<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEventParticipants */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Участники мероприятий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="foreign-event-participants-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить участника?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'firstname',
            'secondname',
            'patronymic',
            'birthdate',

            ['attribute' => 'documents', 'format' => 'raw'],
            ['attribute' => 'achievements', 'format' => 'raw'],
        ],
    ]) ?>

</div>
