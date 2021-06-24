<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventParticipantsWork */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
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

    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'firstname',
            'secondname',
            'patronymic',
            'birthdate',
            'sex',
        ],
    ]) ?>

    <h4><u>Информация об участии в мероприятиях</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'documents', 'format' => 'raw'],
            ['attribute' => 'achievements', 'format' => 'raw'],
            ['attribute' => 'events', 'format' => 'raw'],
        ],
    ]) ?>

    <h4><u>Информация об участии в образовательных программах</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'studies', 'format' => 'raw'],
        ],
    ]) ?>

</div>
