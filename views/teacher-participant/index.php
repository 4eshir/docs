<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTeacherParticipant */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teacher Participants';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-participant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Teacher Participant', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'participant_id',
            'teacher_id',
            'foreign_event_id',
            'branch_id',
            //'focus',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
