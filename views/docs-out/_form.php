<?php

use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOutWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-form">

    <?php $form = ActiveForm::begin(); ?>


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
        ]])->label('Дата документа') ?>

    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true])->label('Тема документа') ?>

    <?php

    $params = [
        'prompt' => 'Выберите корреспондента',
        'onchange' => '
        $.post(
            "' . Url::toRoute('subcat') . '", 
            {id: $(this).val()}, 
            function(res){
                var resArr = res.split("|split|");
                var elem = document.getElementsByClassName("pos");
                elem[0].innerHTML = resArr[0];
                elem = document.getElementsByClassName("com");
                elem[0].innerHTML = resArr[1];
            }
        );
    ',
    ];

    $people = \app\models\work\PeopleWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');

    echo $form->field($model, "correspondent_id")->dropDownList($items,$params)->label('ФИО корреспондента');

    ?>
    <?php
        if ($model->correspondent_id !== null)
        {
            echo '<div id="corr_div1">';
                $position = \app\models\work\PeoplePositionBranchWork::find()->where(['people_id' => $model->correspondent_id])->all();
                $pos_id = [];
                foreach ($position as $posOne)
                    $pos_id[] = $posOne->position_id;
                $position = \app\models\work\PositionWork::find()->where(['in', 'id', $pos_id])->all();
                $items = \yii\helpers\ArrayHelper::map($position,'id','name');
                $params = [
                    'id' => 'position',
                    'class' => 'form-control pos',
                ];
                echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');
            echo '</div>';

            echo '<div id="corr_div2">';
                $company = \app\models\work\CompanyWork::find()->where(['id' => $model->correspondent->company_id])->all();
                $items = \yii\helpers\ArrayHelper::map($company,'id','name');
                $params = [
                    'id' => 'company',
                    'class' => 'form-control com',
                ];
                echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');
            echo '</div>';
        }
        else
        {
            echo '<div id="corr_div1">';
                $positions = \app\models\work\PositionWork::find()->orderBy(['name' => SORT_ASC])->all();
                $items = \yii\helpers\ArrayHelper::map($positions,'id','name');
                $params = [
                    'id' => 'position',
                    'class' => 'form-control pos',
                ];
                echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность корреспондента (при наличии)');
            echo '</div>';

            echo '<div id="corr_div2">';
                $company = \app\models\work\CompanyWork::find()->orderBy(['name' => SORT_ASC])->all();
                $items = \yii\helpers\ArrayHelper::map($company,'id','name');
                $params = [
                    'id' => 'company',
                    'class' => 'form-control com',
                ];
                echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация корреспондента');
            echo '</div>';
        }
    ?>
    

    <?php
    $people = \app\models\work\PeopleWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'signed_id')->dropDownList($items,$params)->label('Кем подписан');

    ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'executor_id')->dropDownList($items,$params)->label('Кто исполнил');

    ?>

    <?php
    $sendMethod= \app\models\work\SendMethodWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($sendMethod,'id','name');
    $params = [];
    echo $form->field($model, 'send_method_id')->dropDownList($items,$params)->label('Способ отправки');

    ?>

    <?= $form->field($model, 'sent_date')->widget(DatePicker::class, [
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
        ]])->label('Дата отправки') ?>

    <div class="padding-v-md">
        <div class="line line-dashed"></div>
    </div>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>

    <?php
    $inoutdocs= \app\models\work\InOutDocsWork::find()->where(['document_out_id' => null])->orWhere(['document_out_id' => $model->id])->all();
    $items = \yii\helpers\ArrayHelper::map($inoutdocs,'id','docInName');
    $params = [];
    if ($model->isAnswer !== null)
    {
        $params = [
            'prompt' => '',
            'options' => [$model->isAnswer => ['Selected' => true]],
        ];
    }
    else
    {
        $params = [
            'prompt' => '',
        ];
    }

    echo $form->field($model, 'isAnswer')->dropDownList($items,$params)->label('Является ответом на');

    ?>

    <?= $form->field($model, 'scanFile')->fileInput(['initialPreview' => $model->imagesLinks, 'initialPreviewAsData' => true, 'overwriteInitial' => false])
        ->label('Скан документа')?>

    <?php
        if (strlen($model->Scan) > 2)
            echo '<h5>Загруженный файл: '.Html::a($model->Scan, \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $model->Scan, 'type' => 'scan'])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['docs-out/delete-file', 'fileName' => $model->Scan, 'modelId' => $model->id, 'type' => 'scan'])).'</h5>';
    ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php
    if ($model->doc !== null)
    {
        $split = explode(" ", $model->doc);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $split[$i], 'type' => 'docs'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['docs-out/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'doc'])).'</td></tr>';
        }
        echo '</table>';
    }
    ?>

    <?= $form->field($model, 'applicationFiles[]')->fileInput(['multiple' => true])->label('Приложения') ?>

    <?php
    if ($model->applications !== null)
    {
        $split = explode(" ", $model->applications);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['docs-out/get-file', 'fileName' => $split[$i], 'type' => 'apps'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['docs-out/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'app'])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <br>
        <?= Html::submitButton('Добавить документ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
