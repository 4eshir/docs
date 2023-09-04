<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\work\SubobjectWork;

use app\models\work\InvoiceWork;
use app\models\work\InvoiceEntryWork;
use app\models\work\ObjectEntryWork;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\common\Invoice */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Редактировать компонент: ' . $model->name;

$invoiceEntry = InvoiceEntryWork::find()->where(['entry_id' => $model->entry_id])->one();
$invoice = InvoiceWork::find()->where(['id' => $invoiceEntry->invoice_id])->one();
$object = ObjectEntryWork::find()->where(['entry_id' => $model->entry_id])->one();

$type = $invoice->type;
$name = ['Накладная', 'Акт', 'УПД', 'Протокол'];
$this->params['breadcrumbs'][] = ['label' => 'Документы о поступлении', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' =>  $name[$type] . ' №' . $invoice->number, 'url' => ['update', 'id' => $invoice->id]];

$this->params['breadcrumbs'][] = ['label' => 'Запись объекта "' . $object->materialObject->name . '"', 'url' => ['update-entry', 'id' => $model->entry_id]];
$this->params['breadcrumbs'][] = 'Редактирование компонента "' . $model->name . '"';
?>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<h1><?= Html::encode($this->title) ?></h1>

<div class="invoice-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput()->label('Наименование компонента') ?>

    <?= $form->field($model, 'characteristics')->textInput()->label('Описание компонента') ?>



    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Список подобъектов</h4></div>
            <div>
                <table class="table table-bordered">
                    <?php

                    $parentObj = SubobjectWork::find()->where(['parent_id' => $model->id])->all();
                    if ($parentObj !== null)
                    {
                        echo '<tr><td style="width: 6%;"><b>№ п/п</b></td><td><b>Название подобъекта</b></td><td><b>Описание</b></td><td><b>Состояние</b></td><td></td></tr>';
                        $i = 1;
                        foreach ($parentObj as $one)
                        {
                            echo '<tr><td>'.$i.'.'.$j.'</td><td>'.$one->name.'</td><td>'.$one->characteristics.'</td><td>'.$one->stateString.'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['invoice/delete-object', 'id' => $one->id, 'modelId' => $one->id, 'from' => 'object']), ['class' => 'btn btn-danger']).'</td></tr>';
                            $i++;
                        }
                    }
                    

                    ?>
                </table>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items4', // required: css class selector
                    'widgetItem' => '.item4', // required: css class
                    'limit' => 100, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item4', // css class
                    'deleteButton' => '.remove-item4', // css class
                    'model' => $modelSubobject[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items4" ><!-- widgetContainer -->
                    <?php foreach ($modelSubobject as $i => $modelSubobjectOne): ?>
                        <div class="item4 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Подобъект</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item4 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item4 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <?= $form->field($modelSubobjectOne, "[{$i}]name")->textInput()->label('Наименование подобъекта'); ?>
                                </div>
                                <div class="col-xs-12">
                                    <?= $form->field($modelSubobjectOne, "[{$i}]characteristics")->textarea(['rows' => '4'])->label('Описание подобъекта'); ?>
                                </div>
                                <div class="col-xs-12">
                                    <?= $form->field($modelSubobjectOne, "[{$i}]state")->checkbox(['checked' => 'checked', 'disabled' => 'disabled']); ?>
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
