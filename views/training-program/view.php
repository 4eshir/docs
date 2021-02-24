<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\TrainingProgram */

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'ped_council_date',
            'ped_council_number',
            'authorName',
            'capacity',
            'student_left_age',
            'student_right_age',
            'focus',
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
        ],
    ]) ?>

</div>
