<?php

use app\models\work\TrainingGroupParticipantWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupWork */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Группа '.$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-group-view">

    <h1><?= Html::encode('Группа '.$this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить группу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="content-container" style="color: #ff0000; font: 18px bold;">
        <?php
            $error = $model->getErrorsWork();
            if ($error != '')
            {
                echo '<p style="">';
                echo $error;
                echo '</p>';
            }
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'branchWork', 'label' => 'Отдел производящий учет', 'format' => 'html'],
            'number',
            ['attribute' => 'budgetText', 'label' => 'Форма обучения'],
            ['attribute' => 'programName', 'format' => 'html'],
            ['attribute' => 'teachersList', 'format' => 'html'],
            'start_date',
            'finish_date',
            ['attribute' => 'countParticipants', 'label' => 'Количество учеников', 'format' => 'html'],
            ['attribute' => 'participantNames', 'format' => 'html'],
            ['attribute' => 'countLessons', 'label' => 'Количество занятий в расписании', 'format' => 'html'],
            ['attribute' => 'lessonDates', 'format' => 'html'],
            ['attribute' => 'journalLink', 'format' => 'raw', 'label' => 'Журнал'],
            ['attribute' => 'ordersName', 'format' => 'html'],
            ['attribute' => 'photos', 'value' => function ($model) {
                $split = explode(" ", $model->photos);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'photos'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['attribute' => 'present_data', 'value' => function ($model) {
                $split = explode(" ", $model->present_data);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'present_data'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['attribute' => 'work_data', 'value' => function ($model) {
                $split = explode(" ", $model->work_data);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'work_data'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            'openText',
        ],
    ]) ?>

</div>
