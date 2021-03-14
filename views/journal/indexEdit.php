<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\JournalModel */

$this->title = 'Участники мероприятий';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <?php
    echo Html::a("Переключиться в режим просмотра", \yii\helpers\Url::to(['journal/index', 'group_id' => $model->trainingGroup]), ['class'=>'btn btn-success'])
    ?>
</div>
<?php
    $parts = \app\models\common\TrainingGroupParticipant::find()->where(['training_group_id' => $model->trainingGroup])->all();
    $lessons = \app\models\common\TrainingGroupLesson::find()->where(['training_group_id' => $model->trainingGroup])->all();
    $form = ActiveForm::begin();

    echo '<br><h4>Журнал посещений<i> (</i>&#128505;<i> - посещение, </i>&#9633;<i> - неявка)</i></h4><table class="table table-bordered">';
    echo '<tr><td>ФИО ученика / Даты занятий</td>';
    foreach ($lessons as $lesson)
    {
        echo $form->field($model, 'lessons[]')->hiddenInput(['value'=> $lesson->id])->label(false);
        echo "<td>".date("d.m", strtotime($lesson->lesson_date))."</td>";
    }
    echo '</tr>';
    foreach ($parts as $part)
    {
        echo '<tr><td style="padding: 5px 0 0 10px">'.$part->participant->shortName.'</td>';
        echo $form->field($model, 'participants[]')->hiddenInput(['value'=> $part->participant_id])->label(false);
        foreach ($lessons as $lesson)
        {
            $visits = \app\models\common\Visit::find()->where(['training_group_lesson_id' => $model->trainingGroup])->andWhere(['foreign_event_participant_id' => $part->participant->id])->one();
            if ($visits == null || $visits->status == 0)
                echo "<td style='padding: 5px 0 0 10px'>".$form->field($model, 'visits[]', ['template' => "{label}\n{input}", 'options' => ['display' => 'block']])->checkbox(['label' => ''])."</td>";
            else
                echo '<td>'.$visits->status.'</td>';
        }
        echo '</tr>';
    }
    echo '</table><br>';
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    ActiveForm::end();
?>
