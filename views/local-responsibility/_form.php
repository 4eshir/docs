<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\LocalResponsibility */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="local-responsibility-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $rt = \app\models\common\ResponsibilityType::find()->all();
    $items = \yii\helpers\ArrayHelper::map($rt,'id','name');
    $params = [
    ];
    echo $form->field($model, 'responsibility_type_id')->dropDownList($items,$params);

    ?>

    <?php
    $branchs = \app\models\common\Branch::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [
        'prompt' => '--',
        'onchange' => '
            $.post(
                "' . Url::toRoute('subcat') . '", 
                {id: $(this).val()}, 
                function(res){
                    var elem = document.getElementsByClassName("aud");
                    elem[0].innerHTML = res;
                }
            );
        ',
    ];
    echo $form->field($model, 'branch_id')->dropDownList($items,$params);

    ?>

    <?php
    //$auds = \app\models\common\Auditorium::find()->all();
    //$items = \yii\helpers\ArrayHelper::map($auds,'id','name');
    $params = [
        'class' => 'form-control aud',
    ];
    echo $form->field($model, 'auditorium_id')->dropDownList([],$params);

    ?>

    <?php
    $peoples = \app\models\common\People::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($peoples,'id','fullName');
    $params = [
        'prompt' => '--'
    ];
    echo $form->field($model, 'people_id')->dropDownList($items,$params);

    ?>

    <?php
    $regs = \app\models\common\Regulation::find()->all();
    $items = \yii\helpers\ArrayHelper::map($regs,'id','name');
    $params = [
    ];
    echo $form->field($model, 'regulation_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'filesStr[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->files) > 2)
    {
        $split = explode(" ", $model->files);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['local-responsibility/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['local-responsibility/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
