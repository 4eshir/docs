<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\common\Invoice */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .invoice-btn {
        margin-right: 10px;
    }

    .btn-item {
        width: 24px;
    }

    .i-item:before {
        content: '↕';
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<script>

    function switchBlock(idBlock) {
        document.querySelector('#invoice').hidden = true;
        document.querySelector('#records').hidden = true;
        block = '#' + idBlock;
        document.querySelector(block).hidden = false;
    }

    function blocksView(event) {
        var item = event.target.parentNode.parentNode.parentNode.parentNode;
        if (item.getElementsByClassName('panel-body')[0].style.display == 'none')
            item.getElementsByClassName('panel-body')[0].style.display = '';
        else
            item.getElementsByClassName('panel-body')[0].style.display = 'none';

        var name = item.getElementsByClassName('panel-body')[0].childNodes[1].childNodes[1].childNodes[3].value;
        if (name != '')
            item.getElementsByClassName('panel-title')[0].innerText = name;
        else
            item.getElementsByClassName('panel-title')[0].innerText = 'Пустая запись';
    }

    function ChangeIds()
    {
        let elems1 = document.getElementsByClassName('change-type');
        let elems2 = document.getElementsByClassName('state-div');

        for (let i = 0; i < elems1.length; i++)
        {
            elems1[i].id = 'type_'+ (elems1.length - i);
            let str1 = 'state_'+ (elems1.length - i);
            elems2[i].id = str1;

            elems1[i].setAttribute("onchange", "OnChangeType(this, '" + str1 + "')");
        }
    }

    function OnChangeType(obj, elem)
    {
        let element = document.getElementById(elem);
        if (obj.value == 2)
            element.style.display = "block";
        else
            element.style.display = "none";
    }
</script>


<div class="invoice-form">

    <?php
    echo Html::button('Показать данные документа', ['class' => 'btn btn-primary invoice-btn', 'onclick' => 'switchBlock("invoice")']);
    if (!($model->number === null || $model->type === null || $model->date_invoice === null))
    {
        echo Html::button('Показать записи документа', ['class' => 'btn btn-primary invoice-btn', 'onclick' => 'switchBlock("records")']);
    }
    ?>
    <div style="height: 20px"></div>


    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div id="invoice">
        <?= $form->field($model, 'type')->radioList(array('0' => 'Накладная', '1' => 'Акт', '2' => 'УПД', '3' => 'Протокол'),
                                [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $checkStr = "";
                                        if ($checked == 1)
                                            $checkStr = "checked";
                                        $return = '<label class="modal-radio">';
                                        $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3" '.$checkStr.'>';
                                        $return .= '<i></i>';
                                        $return .= '<span style="margin-left: 5px">' . ucwords($label) . '</span>';
                                        $return .= '</label><br>';

                                        return $return;
                                    }
                                ])->label('Вид документа') ?>

        <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'style' => 'width: 60%', 'type' => 'number']) ?>

        <?php
        $companies = \app\models\work\CompanyWork::find()->where(['is_contractor' => 1])->orderBy(['name' => SORT_ASC])->all();
        $items = \yii\helpers\ArrayHelper::map($companies,'id','name');
        $params = [
            'style' => 'width: 60%'
        ];
        echo $form->field($model, 'contractor_id')->dropDownList($items,$params);

        ?>

        <?php echo $form->field($model, 'date_product')->widget(\yii\jui\DatePicker::class,
            [
                'dateFormat' => 'php:Y-m-d',
                'language' => 'ru',
                'options' => [
                    'style' => 'width: 60%',
                    'placeholder' => 'Дата',
                    'class'=> 'form-control',
                    'autocomplete'=>'off',
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '2000:2100',
                ]])
        ?>

        <?php echo $form->field($model, 'date_invoice')->widget(\yii\jui\DatePicker::class,
            [
                'dateFormat' => 'php:Y-m-d',
                'language' => 'ru',
                'options' => [
                    'style' => 'width: 60%',
                    'placeholder' => 'Дата',
                    'class'=> 'form-control',
                    'autocomplete'=>'off',
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '2000:2100',
                ]])
        ?>

        <?= $form->field($model, 'documentFile')->fileInput() ?>
        <?php
        if (strlen($model->document) > 2)
            echo '<h5>Загруженный файл: '.Html::a($model->document, \yii\helpers\Url::to(['invoice/get-file', 'fileName' => $model->document, 'modelId' => $model->id])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['invoice/delete-file', 'fileName' => $model->document, 'modelId' => $model->id])).'</h5><br>';
        ?>
    </div>

    <div id="records" hidden>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Записи</h4></div>
                <div>
                    <?php
                    $inEntry = \app\models\work\InvoiceEntryWork::find()->where(['invoice_id' => $model->id])->all();

                    if ($inEntry !== null)
                    {
                        echo '<table class="table table-bordered">';
                        echo '<tr><td><b>Объект</b></td><td><b>Признак</b></td><td><b>Кол-во</b></td><td></td><td></td></tr>';
                        foreach ($inEntry as $entry) {
                            $object = \app\models\work\ObjectEntryWork::find()->where(['entry_id' => $entry->entry_id])->orderBy(['id' => 'SORT_ASC'])->all();
                            if ($object !== null) {
                                echo '<tr><td style="width: 50%"><h5>'.$object[0]->materialObject->name.'</h5></td>
                                    <td style="width: 15%">'.$object[0]->materialObject->attribute.'</td>
                                    <td style="width: 15%">'.$entry->entry->amount.'</td>
                                    <td style="width: 10%">'.Html::a('Редактировать', \yii\helpers\Url::to(['invoice/update-entry', 'id' => $entry->entry->id,  'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td><td style="width: 10%">'.Html::a('Удалить', \yii\helpers\Url::to(['invoice/delete-entry', 'id' => $entry->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                            }
                        }
                        echo '</table>';
                    }
                    ?>
                </div>
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item1', // required: css class
                        'limit' => 10, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $modelObjects[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'eventExternalName',
                        ],
                    ]); ?>


                <div class="container-items" ><!-- widgetContainer -->
                    <?php foreach ($modelObjects as $i => $modelObject): ?>
                        <div class="item1 panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Запись</h3>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-primary btn-xs btn-item" onclick="blocksView(event)"><i class="glyphicon i-item"></i> </button>
                                    <button type="button" class="add-item btn btn-success btn-xs" onclick="ChangeIds()"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div class="col-xs-4">
                                    
                                        <?= $form->field($modelObject, "[{$i}]name")->textInput(['maxlength' => true]) ?>

                                        <?php
                                        $items = ['ОС' => 'ОС', 'ТМЦ' => 'ТМЦ'];
                                        $params = [
                                            'class' => 'form-control oc-type',
                                            'style' => 'width: 30%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]attribute")->dropDownList($items,$params);

                                        ?>

                                        <?= $form->field($modelObject, "[{$i}]amount")->textInput(['type' => 'number', 'value' => 1]) ?>

                                        

                                        <?= $form->field($modelObject, "[{$i}]photoFile")->fileInput(['multiple' => false]) ?>

                                        <?= $form->field($modelObject, "[{$i}]price")->textInput(['type' => 'number', 'style' => 'width: 60%']) ?>

                                        

                                        <?php
                                        $finances = \app\models\work\FinanceSourceWork::find()->orderBy(['name' => SORT_ASC])->all();
                                        $items = \yii\helpers\ArrayHelper::map($finances,'id','name');
                                        $params = [
                                            'style' => 'width: 70%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]finance_source_id")->dropDownList($items,$params);

                                        ?>

                                        

                                        <?php
                                        $kinds = \app\models\work\KindObjectWork::find()->orderBy(['name' => SORT_ASC])->all();
                                        $items = \yii\helpers\ArrayHelper::map($kinds,'id','name');

                                        $params = [
                                            'prompt' => '--',
                                            'style' => 'width: 70%',
                                            'onchange' => '
                                            $.post(
                                                "' . Url::toRoute(['subcat', 'modelId' => $modelObject->id, 'dmId' => "{$i}"]) . '", 
                                                {id: $(this).val()}, 
                                                function(res){
                                                    let elems = document.getElementsByClassName("chars");
                                                    elems[elems.length - 1].innerHTML = res;
                                                    elems = document.getElementsByClassName("main-ch");
                                                    for (let i = 0; i < elems.length; i++)
                                                    {
                                                        let subs = elems[i].getElementsByClassName("ch");
                                                        console.log(subs);
                                                        for (let j = 0; j < subs.length; j++)
                                                            subs[j].setAttribute("name", "MaterialObjectWork[" + i + "][characteristics][]");
                                                    }
                                                }
                                            );
                                        ',
                                        ];
                                        echo $form->field($modelObject, "[{$i}]kind_id")->dropDownList($items,$params);

                                        ?>

                                        <div class="chars">
                                            <?php 

                                            if ($modelObject->kind_id !== null)
                                            {
                                                $characts = \app\models\work\KindCharacteristicWork::find()->where(['kind_object_id' => $modelObject->kind_id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
                                                echo '<div style="border: 1px solid #D3D3D3; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; margin-bottom: 20px; border-radius: 5px; width: 35%">';
                                                foreach ($characts as $c)
                                                {
                                                    $value = \app\models\work\ObjectCharacteristicWork::find()->where(['material_object_id' => $modelObject->id])->andWhere(['characteristic_object_id' => $c->id])->one();
                                                    $val = null;
                                                    if ($value !== null)
                                                    {
                                                        if ($value->integer_value !== null) $val = $value->integer_value;
                                                        if ($value->double_value !== null) $val = $value->double_value;
                                                        if (strlen($value->string_value) > 0) $val = $value->string_value;
                                                    }

                                                    $type = "text";
                                                    if ($c->characteristicObjectWork->value_type == 1 || $c->characteristicObjectWork->value_type == 2) $type = "number";
                                                    //echo $form->field($modelObject, "[{$i}]characteristics[]")->textInput(['type' => $type])->label($c->characteristicObjectWork->name);
                                                    echo '<div style="width: 50%; float: left; margin-top: 10px"><span>'.$c->characteristicObjectWork->name.': </span></div><div style="margin-top: 10px; margin-right: 0; min-width: 40%"><input type="'.$type.'" class="form-inline" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" name="MaterialObjectWork['."{$i}".'][characteristics][]" value="'.$val.'"></div>';
                                                }
                                                echo '</div>';
                                            }

                                            ?>
                                        </div>

                                        <?= $form->field($modelObject, "[{$i}]is_education", ['options' => ['style' => 'width: 200%']])->checkbox() ?>
                                    
                                        <?php
                                        $items = [1 => 'Нерасходуемый', 2 => 'Расходуемый'];
                                        $params = [
                                            'onchange' => 'OnChangeType(this, "state_0")',
                                            'class' => 'form-control change-type',
                                            'style' => 'width: 50%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]type")->dropDownList($items,$params);

                                        ?>



                                        <div id="state_0" class="state-div" style="display: <?php echo $modelObject->type == 2 ? 'block' : 'none'; ?>">
                                            <?= $form->field($modelObject, "[{$i}]state")->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>
                                        </div>

                                        <?= $form->field($modelObject, "[{$i}]damage")->textarea(['rows' => '5']) ?>

                                        <?= $form->field($modelObject, "[{$i}]status")->checkbox(['checked' => true]); ?>

                                        <?php
                                        $items = [0 => 'Списание не требуется', 1 => 'Готов к списанию', 2 => 'Списан'];
                                        $params = [
                                            'style' => 'width: 60%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]write_off")->dropDownList($items,$params);

                                        ?>

                                        <?php echo $form->field($modelObject, "[{$i}]create_date")
                                            ->textInput(['type' => 'date', 'id' => 'inputDate', 'class' => 'form-control inputDateClass'])
                                            /*->widget(\yii\jui\DatePicker::class,
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
                                                ]]) */
                                        ?>

                                        <?php echo $form->field($modelObject, "[{$i}]lifetime")
                                            ->textInput(['type' => 'date', 'id' => 'inputDate', 'class' => 'form-control inputDateClass'])
                                            /*->widget(\yii\jui\DatePicker::class,
                                            [
                                                'dateFormat' => 'php:Y-m-d',
                                                'language' => 'ru',
                                                'options' => [
                                                    'placeholder' => 'Дата окончания эксплуатации',
                                                    'class'=> 'form-control',
                                                    'autocomplete'=>'off',
                                                ],
                                                'clientOptions' => [
                                                    'changeMonth' => true,
                                                    'changeYear' => true,
                                                    'yearRange' => '2000:2100',
                                                ]]) */
                                        ?>

                                        <?php echo $form->field($modelObject, "[{$i}]expirationDate")
                                            ->textInput(['type' => 'date', 'id' => 'inputDate', 'class' => 'form-control inputDateClass'])
                                            /*->widget(\yii\jui\DatePicker::class,
                                            [
                                                'dateFormat' => 'php:Y-m-d',
                                                'language' => 'ru',
                                                'options' => [
                                                    'placeholder' => 'Дата окончания срока годности',
                                                    'class'=> 'form-control',
                                                    'autocomplete'=>'off',
                                                ],
                                                'clientOptions' => [
                                                    'changeMonth' => true,
                                                    'changeYear' => true,
                                                    'yearRange' => '2000:2100',
                                                ]]) */
                                        ?>

                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
