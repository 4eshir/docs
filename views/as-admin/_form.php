<?php

use yii\jui\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\common\AsAdmin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="as-admin-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [];
    echo $form->field($model, 'copyright_id')->dropDownList($items,$params)->label('Правообладатель ПО');

    ?>

    <?= $form->field($model, 'as_name')->textInput()->label('Наименование ПО') ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [];
    echo $form->field($model, 'as_company_id')->dropDownList($items,$params)->label('Реквизиты: организация');

    ?>

    <?= $form->field($model, 'document_number')->textInput()->label('Реквизиты: номер документа') ?>

    <?= $form->field($model, 'document_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]])->label('Реквизиты: дата документа') ?>



    <?= $form->field($model, 'count')->textInput()->label('Количество экземпляров') ?>

    <?= $form->field($model, 'price')->textInput()->label('Стоимость') ?>

    <?= $form->field($model, 'useStartDate')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'оставить поле пустым, если бессрочно',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]])->label('Дата начала использования') ?>

    <?= $form->field($model, 'useEndDate')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'оставить поле пустым, если бессрочно',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]])->label('Дата окончания использования') ?>

    <?php
    $country = \app\models\common\Country::find()->all();
    $items = \yii\helpers\ArrayHelper::map($country,'id','name');
    $params = [];
    echo $form->field($model, 'country_prod_id')->dropDownList($items,$params)->label('Страна производитель');

    ?>

    <?= $form->field($model, 'unifed_register_number')->textInput()->label('Номер в едином реестре ПО') ?>


    <?php
    $lic = \app\models\common\DistributionType::find()->all();
    $items = \yii\helpers\ArrayHelper::map($lic,'id','name');
    $params = [];
    echo $form->field($model, 'distribution_type_id')->dropDownList($items,$params)->label('Способ распространения');

    ?>

    <?php
    $lic = \app\models\common\License::find()->all();
    $items = \yii\helpers\ArrayHelper::map($lic,'id','name');
    $params = [];
    echo $form->field($model, 'license_id')->dropDownList($items,$params)->label('Вид лицензии');

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Установлено</h4></div>
            <?php
            $asInstall = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->all();
            if ($asInstall != null)
            {
                echo '<table>';
                foreach ($asInstall  as $asInstallOne) {
                    echo '<tr><td style="padding-left: 20px"><h4>"'.$asInstallOne->branch->name.'" Кабинет: '.$asInstallOne->cabinet.' ('.$asInstallOne->count.' шт.)</h4></td><td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['as-admin/delete-install', 'id' => $asInstallOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelAsInstall[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [

                        'cabinet',
                        'count',
                    ],
                ]); ?>

                <div class="container-items1" ><!-- widgetContainer -->
                    <?php foreach ($modelAsInstall as $i => $modelAsInstallOne): ?>
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left"></h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body" style="display:inline-block">
                                <?php
                                // necessary for update action.
                                if (! $modelAsInstallOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelAsInstallOne, "[{$i}]id");
                                }
                                ?>
                                <div style="display:inline-block">
                                    <?php

                                    $branch = \app\models\common\Branch::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                    $params = [];
                                    echo $form->field($modelAsInstallOne, "[{$i}]branch_id", ['options' => ['class' => 'col-md-4', ]])->dropDownList($items,$params)->label('Филиал');

                                    echo $form->field($modelAsInstallOne, "[{$i}]cabinet", ['options' => ['class' => 'col-md-4', ]])->textInput()->label('Кабинет');

                                    echo $form->field($modelAsInstallOne, "[{$i}]count", ['options' => ['class' => 'col-md-4', ]])->textInput()->label('Кол-во экземпляров')

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

    <?php
    $lic = \app\models\common\AsType::find()->all();
    $items = \yii\helpers\ArrayHelper::map($lic,'id','type');
    $params = [];
    echo $form->field($model, 'as_type_id')->dropDownList($items,$params)->label('Тип ПО');

    ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true])->label('Примечание') ?>

    <?= $form->field($model, 'scanFile')->textInput(['maxlength' => true])->fileInput()->label('Договор (скан)') ?>

    <?php
    if (strlen($model->scan) > 3)
        echo '<table><tr><td>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => $model->scan])).'</td><td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['as-admin/delete-file-scan', 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr></table><br>';
    ?>

    <?= $form->field($model, 'licenseFile')->textInput(['maxlength' => true])->fileInput()->label('Лицензия') ?>

    <?php
    if (strlen($model->license_file) > 3)
        echo '<table><tr><td>Загруженный файл: '.Html::a($model->license_file, \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => $model->license_file])).'</td><td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['as-admin/delete-file-license', 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr></table><br>';
    ?>

    <?= $form->field($model, 'commercialFiles[]')->fileInput(['multiple' => true])->label('Коммерческие предложения') ?>

    <?php
    if ($model->commercial_offers !== null)
    {
        $split = explode(" ", $model->commercial_offers);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл : '.Html::a($split[$i], \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['as-admin/delete-file-commercial', 'fileName' => $split[$i], 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <?= $form->field($model, 'serviceNoteFile[]')->fileInput(['multiple' => true])->label('Служебные записки') ?>

    <?php
    if ($model->service_note !== null)
    {
        $split = explode(" ", $model->service_note);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл : '.Html::a($split[$i], \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['as-admin/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>