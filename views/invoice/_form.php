<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\common\Invoice */
/* @var $form yii\widgets\ActiveForm */
?>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>


<?php

$js =<<< JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        /*alert('item');*/
    })
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);

?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'style' => 'width: 60%', 'type' => 'number']) ?>

    <?php
    $companies = \app\models\work\CompanyWork::find()->where(['is_contractor' => 1])->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($companies,'id','name');
    $params = [
        'style' => 'width: 60%'
    ];
    echo $form->field($model, 'contractor_id')->dropDownList($items,$params);

    ?>

    <?php echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::class,
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

    <?= $form->field($model, 'type')->radioList(array('0' => 'Накладная', '1' => 'Акт'), 
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

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Записи</h4></div>
            <div>
                <?php
                $entries = \app\models\work\InvoiceEntryWork::find()->where(['invoice_id' => $model->id])->all();
                if ($entries != null)
                {
                    echo '<table class="table table-bordered">';
                    echo '<tr><td><b>Объект</b></td><td><b>Кол-во</b></td><td></td><td></td></tr>';
                    foreach ($entries as $entry) {
                            echo '<tr><td style="width: 60%"><h5>'.$entry->entryWork->objectWork->name.'</h5></td><td style="width: 20%">'.$entry->entryWork->amount.'</td><td style="width: 10%">'.Html::a('Редактировать', \yii\helpers\Url::to(['invoice/update-entry', 'id' => $entry->entry->id,  'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td><td style="width: 10%">'.Html::a('Удалить', \yii\helpers\Url::to(['invoice/delete-entry', 'id' => $entry->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
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
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Запись</h3>
                                <div class="pull-right">
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
                                            'onchange' => 'OnChangeOC(this, "no-oc_0", "oc_0")',
                                            'style' => 'width: 30%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]attribute")->dropDownList($items,$params);

                                        ?>

                                        <div id="no-oc_0" style="display: none" class="no-oc">
                                            <?= $form->field($modelObject, "[{$i}]amount")->textInput(['type' => 'number']) ?>
                                        </div>

                                        <div id="oc_0" style="display: block" class="oc">
                                            <?= $form->field($modelObject, "[{$i}]inventory_number")->textInput(['maxlength' => true, 'style' => 'width: 70%']) ?>
                                        </div>
                                        

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

                                        <?php
                                        $items = [1 => 'Нерасходуемый', 2 => 'Расходуемый'];
                                        $params = [
                                            'onchange' => 'OnChangeType(this, "state_0")',
                                            'class' => 'form-control change-type',
                                            'style' => 'width: 50%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]type")->dropDownList($items,$params);

                                        ?>

                                        <?= $form->field($modelObject, "[{$i}]is_education", ['options' => ['style' => 'width: 200%']])->checkbox() ?>

                                        <div id="state_0" class="state-div" style="display: <?php echo $modelObject->type == 2 ? 'block' : 'none'; ?>">
                                            <?= $form->field($modelObject, "[{$i}]state")->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>
                                        </div>

                                        <?= $form->field($modelObject, "[{$i}]damage")->textarea(['rows' => '5']) ?>

                                        <?= $form->field($modelObject, "[{$i}]status")->checkbox(); ?>

                                        <?php
                                        $items = [0 => '-', 1 => 'Готов к списанию', 2 => 'Списан'];
                                        $params = [
                                            'style' => 'width: 50%'
                                        ];
                                        echo $form->field($modelObject, "[{$i}]write_off")->dropDownList($items,$params);

                                        ?>

                                        <?php echo $form->field($modelObject, "[{$i}]create_date")->widget(\yii\jui\DatePicker::class,
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

                                        <?php echo $form->field($modelObject, "[{$i}]lifetime")->widget(\yii\jui\DatePicker::class,
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
                                                ]]) 
                                        ?>

                                        <?php echo $form->field($modelObject, "[{$i}]expirationDate")->widget(\yii\jui\DatePicker::class,
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
                                                ]]) 
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

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function ChangeIds()
    {
        let elems1 = document.getElementsByClassName('change-type');
        let elems11 = document.getElementsByClassName('oc-type');
        let elems2 = document.getElementsByClassName('state-div');
        let elems3 = document.getElementsByClassName('no-oc');
        let elems4 = document.getElementsByClassName('oc');
        for (let i = 0; i < elems1.length; i++)
        {
            elems1[i].id = 'type_'+ (elems1.length - i);
            let str1 = 'state_'+ (elems1.length - i);
            elems2[i].id = str1;
            let str2 = 'no-oc_'+ (elems1.length - i);
            elems3[i].id = str2;
            let str3 = 'oc_'+ (elems1.length - i);
            elems4[i].id = str3;
            elems1[i].setAttribute("onchange", "OnChangeType(this, '" + str1 + "')");
            elems11[i].setAttribute("onchange", "OnChangeOC(this, '" + str2 + "', '" + str3 + "')");
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

    function OnChangeOC(obj, elem1, elem2)
    {
        let element1 = document.getElementById(elem1);
        let element2 = document.getElementById(elem2);
        if (obj.value !== 'ОС')
        {
            element1.style.display = "block";
            element2.style.display = "none";
        }
        else
        {
            element1.style.display = "none";
            element2.style.display = "block";
        }
    }
</script>
