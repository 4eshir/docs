<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\KindObject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kind-object-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Характеристики</h4></div>
            <div>
                <?php
                $chars = \app\models\work\KindCharacteristicWork::find()->where(['kind_object_id' => $model->id])->all();
                if ($chars != null)
                {
                    echo '<table class="table table-bordered">';
                    echo '<tr><td><b>Название</b></td><td><b>Тип данных</b></td><td><b>Знач. вып. списка</b></td></tr>';
                    foreach ($chars as $char) {
                        echo '<tr><td><h5>'.$char->characteristicObjectWork->name.'</h5></td><td><h5>'.$char->characteristicObjectWork->valueTypeStr.'</h5></td><td>'.
                            $char->characteristicObjectWork->ddValueStr.'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['kind-object/delete-characteristic', 'id' => $char->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper5', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items5', // required: css class selector
                    'widgetItem' => '.item5', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelCharacteristics[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items5" ><!-- widgetContainer -->
                    <?php foreach ($modelCharacteristics as $i => $modelCharacteristic): ?>
                        <div class="item5 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Характеристика</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (!$modelCharacteristic->isNewRecord) {
                                    echo Html::activeHiddenInput($modelCharacteristic, "[{$i}]id");
                                }
                                ?>
                                <div class="col-xs-4">
                                    <?php
                                    $charNames = \app\models\work\CharacteristicObjectWork::find()->select(['name as value', 'name as label'])->asArray()->all();
                                    echo $form->field($modelCharacteristic, "[{$i}]name")->widget(
                                        AutoComplete::className(), [
                                        'clientOptions' => [
                                            'source' => $charNames,
                                            'select' => new JsExpression("function( event, ui ) {
                                                $('#names' + counter).val(ui.item.id); //#memberssearch-family_name_id is the id of hiddenInput.
                                             }"),
                                        ],
                                        'options'=>[
                                            'class'=>'form-control on part',
                                            'id' => 'names0',
                                        ]
                                    ])->label('Название характеристики'); ?>
                                </div>


                                <div class="col-xs-4">
                                    <?php
                                    $items = ['1' => 'Целое число', '2' => 'Дробное число', '3' => 'Строковое значение', '4' => 'Булевое', '5' => 'Дата',
                                                '6' => 'Файл', '7' => 'Выпадающий список'];
                                    $params = [];
                                    echo $form->field($modelCharacteristic, "[{$i}]value_type")->dropDownList($items,$params)->label('Тип данных');
                                    ?>
                                </div>

                                <div class="col-xs-4">
                                    <?php echo $form->field($modelCharacteristic, "[{$i}]dd_value")->textarea(['rows' => '5'])->label('Значения для выпадающего списка'); ?>
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
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script>
    let counter = 0;
</script>

<?php

$charNames = \app\models\work\CharacteristicObjectWork::find()->select(['name as value', 'name as label'])->asArray()->all();
$jsonNames = json_encode($charNames);

$js =<<< JS
    $(".dynamicform_wrapper5").on("click", ".on", function(e) {
        console.log($jsonNames);
      if ( !$(this).data("autocomplete") ) {
          e.preventDefault();
          $(this).autocomplete({
            source: $jsonNames,
            select: function( event, ui ) {
                $('#names' + counter).val(ui.item.id);
            }
          });
      }
    })
JS;

$js1 =<<< JS
    $(".dynamicform_wrapper5").on("afterInsert", function(e, item) {
        counter = counter + 1;
        var elems1 = document.getElementsByClassName('part');
        elems1[elems1.length - 1].id = 'names' + counter;
    });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
$this->registerJs($js1, \yii\web\View::POS_LOAD);


?>

<?php

?>
