<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingProgramWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-program-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить программу?',
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
            'name',
            'ped_council_date',
            'ped_council_number',
            ['attribute' => 'compilers', 'format' => 'html'],
            'capacity',
            'student_left_age',
            'student_right_age',
            'stringFocus',
            ['attribute' => 'trueName', 'label' => 'Тематическое направление', 'value' => function($model) {return $model->thematicDirection->full_name . ' (' . $model->thematicDirection->name . ')';}],
            'hour_capacity',
            ['attribute' => 'themesPlan', 'format' => 'raw', 'label' => 'Учебно-тематический план'],
            ['attribute' => 'branchs', 'format' => 'raw'],
            ['attribute' => 'allow_remote', 'value' => function($model) {return $model->allow_remote == 0 ? 'Нет' : 'Да';}],
            ['attribute' => 'doc_file', 'value' => function ($model) {
                return Html::a($model->doc_file, \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $model->doc_file, 'modelId' => $model->id, 'type' => 'doc']));
            }, 'format' => 'raw'],
            ['attribute' => 'edit_docs', 'value' => function ($model) {
                $split = explode(" ", $model->edit_docs);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'edit_docs'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            'key_words',
            ['attribute' => function($model) {return $model->actual == 0 ? 'Нет' : 'Да';}, 'label' => 'Образовательная программа актуальна'],
        ],
    ]) ?>

</div>
