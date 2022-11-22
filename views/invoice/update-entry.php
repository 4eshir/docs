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

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<style>
    td:first-child {
        width: 30px;
        font-weight: 600;
    }

    td > input {
        margin: 5px;
    }

    .main_dynamic {
        border: 1px solid #F5F5F5;
        background: white;
    }

    .head_dynamic {
        margin-bottom: 20px;
        background: #F5F5F5;
        border-radius: 4px;
        padding: 5px;
        font-weight: bold;
        font-size: 16px;
        height: 40px;

        display: inline-flex
        width: 100%
        justify-content: space-between
    }

    .head_dynamic_text {
        width: 90%;
        float: left;
    }

    .head_dynamic_action {
        width: 10%;
        float: left; 
    }

    .head_note_text {
        width: 90%;
        float: left;
    }

    .head_note_action {
        margin-left: auto;
        margin-right: 0;
        width: 10%;
        float: left; 
    }

    .content_dynamic {
        padding-right: 20px;
        padding-bottom: 20px;
    }

    .main_note {
        border: 1px solid #F5F5F5;
        background: white;
        margin-bottom: 15px;
        margin-left: 40px;
        margin-top: 20px;
    }

    .head_note {
        margin-bottom: 15px;
        background: #F5F5F5;
        border-radius: 4px;
        padding: 5px;
        height: 40px;

        display: inline-flex
        width: 100%
        justify-content: space-between
    }

    .content_note {
        margin: 15px;
    }

    .add_button {
        height: 30px;
        width: 35%;
        border-radius: 5px;
        background: #5cb85c;
        border: 0;
        font-weight: bold;
        color: white;
        margin-right: 10px;
        font-size: 18px;
    }

    .remove_button {
        height: 30px;
        width: 35%;
        border-radius: 5px;
        background: #d9534f;
        border: 0;
        font-weight: bold;
        color: white;
        margin-right: 10px;
        font-size: 18px;
    }

</style>

<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div id="oc_0" style="margin-bottom: 5px; display: <?php echo $model->attribute === "ОС" ? 'block' : 'none'; ?>" class="oc">
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

    <?= $form->field($model, 'complex')->checkbox() ?>


    <div id="complex_block" style="display: <?php echo $model->complex == 1 ? 'block' : 'none' ?>; margin-bottom: 20px">
        <div class="main_dynamic">
            <div class="head_dynamic">
                <div class="head_dynamic_text">Введите все составные части объекта</div>
                <div class="head_dynamic_action"><button type="button" class="add_button" onclick="AddHandler(this)">+</button>
                </div>
            </div>
            <div class="content_dynamic">
            
                <!-- Шаблон динамической формы. ОБЯЗАТЕЛЕН ДЛЯ РАБОТЫ -->
                <div class="main_note" style="display: none">
                    <div class="head_note">
                        <div class="head_note_text">Часть объекта</div>
                        <div class="head_note_action"><button type="button" class="add_button" onclick="AddHandler(this)">+</button><button type="button" class="remove_button" onclick="RemoveHandler(this)">&#10006;</button></div>
                    </div>
                    <div class="content_note">
                        <label class="control-label">Наименование объекта</label>
                        <input type="text" class="form-control" name="EntryWork[0][name]" value="" style="margin-bottom: 10px">
                        <label class="control-label">Описание объекта</label>
                        <textarea class="form-control" rows="4" name="EntryWork[0][text]" value=""></textarea>
                    </div>                
                </div>
                <!-- ------------------------------------------------ -->

                
            </div>
            
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    $("#entrywork-complex").change(function() {
        let elem = $("#complex_block")[0];
        if ($(this)[0].checked)
            elem.style.display = "block";
        else
            elem.style.display = "none";
    });

    function AddHandler(this_elem)
    {
        let elem = (this_elem).parentNode;
        if (elem.classList.contains('head_dynamic_action')) //если это добавление частей первого уровня
        {
            let template = this_elem.parentNode.parentNode.parentNode.getElementsByClassName("main_note")[0].cloneNode(true);
            template.style.display = "block";
            template.getElementsByTagName("input")[0].name = "EntryWork[dynamic][" + ((this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note").length - (this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("inside").length) + "][name]";
            template.getElementsByTagName("textarea")[0].name = "EntryWork[dynamic][" + ((this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note").length - (this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("inside").length) + "][text]";
            //template.
            let form = (this_elem).parentNode.parentNode.parentNode.getElementsByClassName("content_dynamic")[0];
            form.append(template);
        }
        else //если добавление подобъектов к объектам
        {
            if ((this_elem).parentNode.parentNode.parentNode.parentNode.classList.contains("content_dynamic"))
            {
                let template = (this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note")[0].cloneNode(true);
                template.style.display = "block";
                template.classList.add('inside');

                 template.getElementsByTagName("input")[0].name = "EntryWork[dynamic][" + ((this_elem).parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note").length - (this_elem).parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("inside").length - 1) + "][" + ((this_elem).parentNode.parentNode.parentNode.getElementsByClassName("inside").length) + "][name]";
                template.getElementsByTagName("textarea")[0].name = "EntryWork[dynamic][" + ((this_elem).parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note").length - (this_elem).parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("inside").length - 1) + "][" + ((this_elem).parentNode.parentNode.parentNode.getElementsByClassName("inside").length) + "][text]";


                let form = (this_elem).parentNode.parentNode.parentNode;
                form.append(template);
            }
            else
            {
                alert('Слишком большая вложенность объектов!');
            }
        }
    }

    function RemoveHandler(this_elem)
    {
        let elem = (this_elem).parentNode;
        if (elem.classList.contains('head_dynamic_action')) //если это удаление частей первого уровня
        {
            /*let template = $(".main_note")[0].cloneNode(true);
            let form = $(this)[0].parentNode.parentNode.parentNode.getElementsByClassName("content_dynamic")[0];
            form.append(template);*/
        }
        else //если удаление объектов и подобъектов
        {
            let suicide_elem = (this_elem).parentNode.parentNode.parentNode;
            suicide_elem.parentNode.removeChild(suicide_elem);
        }
    }
</script>