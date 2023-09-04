<?php

/* @var $this yii\web\View */


?>
<?php \yii\widgets\Pjax::begin(); ?>
<?php $form = \yii\widgets\ActiveForm::begin(); ?>
<?php echo $form->field($model, 'sendMethod'); ?>
<?php echo \yii\helpers\Html::submitButton('Поиск')?>
<?php \yii\widgets\ActiveForm::end(); ?>

<table class="table table-hover table-bordered">
    <tr>
        <td>№ исходящего документа</td>
        <td>Дата исходящего документа</td>
        <td>Тема документа</td>
        <td>Наименование корреспондента</td>
        <td>Кому адресован</td>
        <td>Кем подписан</td>
        <td>Исполнитель</td>
        <td>Способ отправки</td>
        <td>Дата отправки</td>
        <td>Скан документа</td>
        <td>Исходные файлы</td>
        <td>Регистратор документа</td>
        <td>Действия</td>
    </tr>
    <?php
        for ($i = 0; $i < count($model->documents); $i++)
        {
            echo '<tr>';
                echo '<td>'.$model->documents[$i]->id.'</td>';
                echo '<td>'.$model->documents[$i]->document_date.'</td>';
                echo '<td>'.$model->documents[$i]->document_theme.'</td>';
                echo '<td>'.$model->documents[$i]->destination->company->companyType->type.'</td>';
                echo '<td>'.$model->documents[$i]->destination->position->name.' '.$model->documents[$i]->destination->company->name.'</td>';
                echo '<td>'.$model->documents[$i]->signed->firstname.' '.$model->documents[$i]->signed->secondname.' '.$model->documents[$i]->signed->patronymic.'</td>';
                echo '<td>'.$model->documents[$i]->executor->firstname.' '.$model->documents[$i]->executor->secondname.' '.$model->documents[$i]->executor->patronymic.'</td>';
                echo '<td>'.$model->documents[$i]->sendMethod->name.'</td>';
                echo '<td>'.$model->documents[$i]->sent_date.'</td>';
                echo '<td>СКАН</td>';
                echo '<td>ФАЙЛЫ</td>';
                echo '<td>'.$model->documents[$i]->register->firstname.' '.$model->documents[$i]->register->secondname.' '.$model->documents[$i]->register->patronymic.'</td>';
                echo '<td>ТУТ КНОПОЧКИ</td>';
            echo '</tr>';
         }
    ?>
</table>

<?php \yii\widgets\Pjax::end(); ?>
