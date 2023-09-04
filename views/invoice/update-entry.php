<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\work\SubobjectWork;
use app\models\work\ObjectEntryWork;
use app\models\work\ObjectCharacteristicWork;
use app\models\work\InvoiceEntryWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\EntryWork */

$this->title = 'Редактировать материальные объекты ('.$model->attribute.') в записи';
$this->params['breadcrumbs'][] = ['label' => 'Документы о поступлении', 'url' => ['index']];
$type = $model->getInvoiceWork()->type;
$name = ['Накладная', 'Акт', 'УПД', 'Протокол'];
$this->params['breadcrumbs'][] = ['label' =>  $name[$type] . ' №' . $model->getInvoiceWork()->number, 'url' => ['view', 'id' => $model->getInvoiceWork()->id]];
$this->params['breadcrumbs'][] = 'Редактирование ';
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<style>
    tr:first-child {
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
        height: 40px;
        font-weight: bold;
        display: inline-flex;
        width: 100%;
        justify-content: space-between;
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

        display: inline-flex;
        width: 100%;
        justify-content: space-between;

        font-weight: 700;
        font-size: 16px;
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

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

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

    <?= $form->field($model, 'price')->textInput(['style' => 'width: 60%']) ?>

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

    <div class="chars">
        <?php

        if ($model->kind_id !== null)
        {
            $characts = \app\models\work\KindCharacteristicWork::find()->where(['kind_object_id' => $model->kind_id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
            echo '<div style="border: 1px solid #D3D3D3; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; margin-bottom: 20px; border-radius: 5px; width: 55%">';
            echo '<table style="width: 100%">';
            foreach ($characts as $c)
            {
                $value = \app\models\work\ObjectCharacteristicWork::find()->where(['material_object_id' => $model->object_id])->andWhere(['characteristic_object_id' => $c->characteristic_object_id])->orderBy(['id' => SORT_DESC])->one();
                $val = null;
                if ($value !== null)
                {
                    if ($value->integer_value !== null) $val = $value->integer_value;
                    if ($value->double_value !== null) $val = $value->double_value;
                    if (strlen($value->string_value) > 0) $val = $value->string_value;
                    if ($value->bool_value !== null) $val = $value->bool_value;
                    if ($value->date_value !== null) $val = $value->date_value;
                    if (strlen($value->document_value) > 0) $val = $value->document_value;
                    if ($value->dropdown_value !== null) $val = $value->dropdown_value;
                }

                $type = "dropdown";
                if ($c->characteristicObjectWork->value_type == 1 || $c->characteristicObjectWork->value_type == 2) $type = "number";
                else if ($c->characteristicObjectWork->value_type == 3) $type = "text";
                else if ($c->characteristicObjectWork->value_type == 4) $type = "checkbox";
                else if ($c->characteristicObjectWork->value_type == 5) $type = "date";
                else if ($c->characteristicObjectWork->value_type == 6) $type = "file";


                if ($type !== "dropdown")
                {
                    $placeholder = ['Введите число', 'Введите число', 'Введите текст'];
                    $input = '';
                    if ($c->characteristicObjectWork->value_type == 4 && $val == 1)
                        $input = '<input onclick="handleClickOrig(this)" step="any" type="'.$type.'" checked class="form-inline ch" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" content="'.$val.'"><input name="EntryWork[characteristics][]" type="hidden" value="'.$val.'">';
                    else if ($c->characteristicObjectWork->value_type == 4)
                    {
                        $input = '<input onclick="handleClickOrig(this)" step="any" type="'.$type.'" class="form-inline ch" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" content="'.$val.'"><input type="hidden" name="EntryWork[characteristics][]" value="'.$val.'">';
                    }
                    else
                        $input = '<input onclick="handleClickOrig(this)" step="any" type="'.$type.'" placeholder="'.$placeholder[$c->characteristicObjectWork->value_type-1].'" class="form-inline ch" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" name="EntryWork[characteristics][]" value="'.$val.'" content="'.$val.'">';

                    echo '<tr><th style="width: 45%; float: left; margin-top: 10px;">'.$c->characteristicObjectWork->name.'</th>
                     <th style="float: left; margin-top: 10px; padding-left: 3%">'.$input.'</th></tr>';
                }
                else
                {
                    $options = '';
                    $items = \app\models\work\DropdownCharacteristicObjectWork::find()->where(['characteristic_object_id' => $c->characteristicObjectWork->id])->all();

                    foreach ($items as $item)
                    {
                        $selected = $val == $item->id ? 'selected' : '';
                        $options .= '<option value="'.$item->id.'" '.$selected.'>'.$item->item.'</option>';
                    }

                    echo '<tr><th style="width: 45%; float: left; margin-top: 10px;">'.$c->characteristicObjectWork->name.'</th>
                     <th style="float: left; margin-top: 10px; padding-left: 3%">
                     <select step="any" type="'.$type.'" name="EntryWork[characteristics][]">'.$options.'</select></th></tr>';
                }
                
                /*echo '<div style="width: 50%; float: left; margin-top: 10px"><span>'.$c->characteristicObjectWork->name.': </span></div><div style="margin-top: 10px; margin-right: 0; min-width: 40%"><input type="'.$type.'" class="form-inline" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" name="MaterialObjectWork[characteristics][]" value="'.$val.'"></div>';*/
            }
            echo '</table>';
            echo '</div>';
        }

        ?>
    </div>

    <h5><b>Общие документы</b></h5>
    <table class="table table-bordered">
        <?php 

            $ids = [];
            $objEntry = ObjectEntryWork::find()->where(['entry_id' => $model->id])->all();
            foreach ($objEntry as $obj) $ids[] = $obj->material_object_id;


            $docs = ObjectCharacteristicWork::find()->select('document_value')->distinct()->joinWith(['characteristicObject characteristicObject'])->where(['IN', 'material_object_id', $ids])->andWhere(['characteristicObject.value_type' => 6])->andWhere(['!=', 'document_value', 'null'])->all();


            $invoice = InvoiceEntryWork::find()->where(['entry_id' => $model->id])->one();

            foreach ($docs as $doc)
                echo '<tr><td>'.Html::a($doc->document_value, \yii\helpers\Url::to(['invoice/get-entry-file', 'fileName' => $doc->document_value, 'modelId' => $model->id])).'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['invoice/delete-entry-doc', 'name' => $doc->document_value, 'entryId' => $model->id, 'modelId' => $invoice->id]), ['class' => 'btn btn-danger']).'</td></tr>';

        ?>
    </table>

    <?= $form->field($model, 'complex')->checkbox() ?>


    <div id="complex_block" style="display: <?php echo $model->complex == 1 ? 'block' : 'none' ?>; margin-bottom: 20px">
        <div>
            <table class="table table-bordered">
                <?php

                $parentObj = SubobjectWork::find()->where(['entry_id' => $model->id])->all();
                if ($parentObj !== null)
                {
                    echo '<tr><td style="width: 6%;">№ п/п</td><td>Название компонентов</td><td>Описание</td><td>Состояние</td><td></td></tr>';
                    $i = 1;
                    foreach ($parentObj as $one)
                    {
                        echo '<tr><td>'.$i.'</td><td>'.$one->name.'</td><td>'.$one->characteristics.'</td><td>'.$one->stateString.'</td><td>'.Html::a('Редактировать', \yii\helpers\Url::to(['invoice/update-object', 'id' => $one->id]), ['class' => 'btn btn-primary']).'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['invoice/delete-object', 'id' => $one->id, 'modelId' => $model->id, 'from' => 'entry']), ['class' => 'btn btn-danger']).'</td></tr>';
                        $subs = SubobjectWork::find()->where(['parent_id' => $one->id])->all();
                        if ($subs !== null)
                        {
                            $j = 1;
                            foreach ($subs as $sub)
                            {
                                echo '<tr><td>'.$i.'.'.$j.'</td><td>'.$sub->name.'</td><td>'.$sub->characteristics.'</td><td>'.$sub->stateString.'</td><td>'.Html::a('Редактировать', \yii\helpers\Url::to(['invoice/update-object', 'id' => $one->id]), ['class' => 'btn btn-primary', 'disabled' => 'true', 'onclick' => 'return false']).'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['invoice/delete-object', 'id' => $sub->id, 'modelId' => $model->id, 'from' => 'entry']), ['class' => 'btn btn-danger']).'</td></tr>';
                                $j++;
                            }
                        }
                        $i++;
                    }
                }
                

                ?>
            </table>
        </div>

        <div class="main_dynamic">
            <div class="head_dynamic">
                <div class="head_dynamic_text"><h4><i class="fa fa-object-ungroup" aria-hidden="true"></i>Компоненты</h4></div>
                <div class="head_dynamic_action"><button type="button" class="add_button" onclick="AddHandler(this)">+</button>
                </div>
            </div>
            <div class="content_dynamic">
            
                <!-- Шаблон динамической формы. ОБЯЗАТЕЛЕН ДЛЯ РАБОТЫ -->
                <div class="main_note" style="display: none">
                    <div class="head_note">
                        <div class="head_note_text">Компонент объекта</div>
                        <div class="head_note_action"><button type="button" class="add_button" onclick="AddHandler(this)"><i class="fa fa-level-down" aria-hidden="true"></i></button><button type="button" class="remove_button" onclick="RemoveHandler(this)">&#10006;</button></div>
                    </div>
                    <div class="content_note">
                        <label class="control-label">Наименование компонента</label>
                        <input type="text" class="form-control" name="EntryWork[0][name]" value="" style="margin-bottom: 10px">
                        <label class="control-label">Описание компонента</label>
                        <textarea class="form-control" rows="4" name="EntryWork[0][text]" value="" style="margin-bottom: 12px"></textarea>
                        
                        <input type="hidden" name="EntryWork[0][state]" value="1">
                        <label>
                            <input type="checkbox" id="entrywork-complex" name="EntryWork[0][state]" value="1" aria-invalid="false" disabled checked> В рабочем состоянии
                        </label>
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
            template.getElementsByTagName("input")[1].name = "EntryWork[dynamic][" + ((this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note").length - (this_elem).parentNode.parentNode.parentNode.parentNode.getElementsByClassName("inside").length) + "][state]";
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
                template.getElementsByTagName("input")[1].name = "EntryWork[dynamic][" + ((this_elem).parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("main_note").length - (this_elem).parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("inside").length - 1) + "][" + ((this_elem).parentNode.parentNode.parentNode.getElementsByClassName("inside").length) + "][state]";


                let form = (this_elem).parentNode.parentNode.parentNode;
                form.append(template);

                template.getElementsByClassName("head_note_text")[0].textContent = "Подобъект компонента";
                template.getElementsByClassName("content_note")[0].childNodes[1].textContent = "Наименование подобъекта";
                template.getElementsByClassName("content_note")[0].childNodes[5].textContent = "Описание подобъекта";
                template.previousElementSibling.previousElementSibling.getElementsByClassName("head_note_text")[0].innerHTML = "<i class='fa fa-object-group' aria-hidden='true'></i> Компонент объекта";
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

    
    function handleClick(cb)
    {
        if (cb.checked == true)
            cb.previousElementSibling.value = '1';
        else
            cb.previousElementSibling.value = '0';
    }

    function handleClickOrig(cb)
    {
        if (cb.checked == true)
            cb.nextElementSibling.value = '1';
        else
            cb.nextElementSibling.value = '0';
    }

</script>