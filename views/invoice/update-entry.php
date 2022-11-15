<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\EntryWork */

$this->title = 'Редактировать материальные объекты ('.$model->attribute.') в записи';
$this->params['breadcrumbs'][] = ['label' => 'Документы о поступлении', 'url' => ['index']];
$type = $model->getInvoiceWork()->type;
$name = ['Накладная', 'Акт', 'УПД', 'Протокол'];
$this->params['breadcrumbs'][] = ['label' =>  $name[$type] . ' №' . $model->getInvoiceWork()->number, 'url' => ['view', 'id' => $model->getInvoiceWork()->id]];
$this->params['breadcrumbs'][] = 'Редактирование ';
?>

<style>
    td:first-child {
        width: 30px;
        font-weight: 600;
    }

    td > input {
        margin: 5px;
    }
</style>

<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div id="oc_0" style="display: block; margin-bottom: 5px;" class="oc">
        <?php
            if ($model->amount == 1)
                echo '<label class="control-label" for="entrywork-inventory_number">Инвентарный номер</label>';
            else
                echo '<label class="control-label" for="entrywork-inventory_number">Инвентарные номера</label>';

            echo '<table>';
            for ($i = 0; $i < $model->amount; $i++)
            {
                echo '<tr><td>'.($i+1).': </td><td>
                        <input type="number" id="entrywork-inventory_number" class="form-control" name="EntryWork[inventory_number][]" value="'.$model->inventory_number[$i].'">
                    </td></tr>';
            }
            echo '</table>';
         ?>
    </div>

    <?= $form->field($model, 'price')->textInput(['type' => 'number', 'style' => 'width: 60%']) ?>

    <?php echo $form->field($model, 'create_date')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата производства',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <?php echo $form->field($model, 'lifetime')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания эксплуатации (опционально)',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <?php echo $form->field($model, 'expirationDate')->widget(\yii\jui\DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания срока годности (при наличии)',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
