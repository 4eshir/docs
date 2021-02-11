<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\extended\FeedbackAnswer */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Форма обратной связи';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <?php
    $form = ActiveForm::begin(['id' => 'login-form']);
    $feedback = \app\models\common\Feedback::find()->all();
    ?>

    <table class="table table-bordered">
        <tr>
            <td style="width: 33%"><b>Пользователь</b></td>
            <td style="width: 33%"><b>Сообщение</b></td>
            <td style="width: 34%"><b>Ответ</b></td>
        </tr>
        <?php
        $i = 0;
        foreach ($feedback as $feedbackOne)
        {
            echo '<tr><td>'.$feedbackOne->user->secondname.' '.$feedbackOne->user->firstname.' '.$feedbackOne->user->patronymic.'</td>'.
            '<td>'.$feedbackOne->text.'</td><td>'.
            $form->field($model, 'answer[]')->textInput(['value' => $feedbackOne->answer])->label(false).'</td></tr>';
            echo $form->field($model, 'id[]')->hiddenInput(['value' => $feedbackOne->id])->label(false);
            $i = $i + 1;
        }
        ?>
    </table>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>