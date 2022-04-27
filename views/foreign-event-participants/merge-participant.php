<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\jui\AutoComplete;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventParticipantsWork */

$this->title = 'Слияние участников деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Слияние участников деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Слияние', 'url' => ['merge-participant']];
?>
<style>
    .block-report{
        background: #e9e9e9;
        width: 45%;
        padding: 10px 10px 0 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        margin-right: 10px;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Выберите двух участников деятельности</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

        <?php

        $people = \app\models\work\ForeignEventParticipantsWork::find()->select(['CONCAT(secondname, \' \', firstname, \' \', patronymic, \' \', birthdate, \' (id: \', id, \')\') as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic, ' ', birthdate, ' (id: ', id, ')') as label", 'id as id'])->where(['is_true' => 1])->orWhere(['guaranted_true' => 1])->asArray()->all();

        echo $form->field($model, 'fio1')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $people,

                'select' => new JsExpression("function( event, ui ) {
                    $('#participant_id1').val(ui.item.id); //#memberssearch-family_name_id is the id of hiddenInput.
                    CheckFieldsFill();
                 }"),
            ],
            'options'=>[
                'class'=>'form-control on',
            ]
        ])->label('ФИО участника деятельности №1');

        echo $form->field($model, 'id1')->hiddenInput(['class' => 'part', 'id' => 'participant_id1', 'name' => 'participant1'])->label(false);

        ?>


        <!--<input class="part" type="hidden" id="participant_id1" name="participant1">-->
    </div>

    <div class="col-xs-6 block-report">
        <?php

        $people = \app\models\work\ForeignEventParticipantsWork::find()->select(['CONCAT(secondname, \' \', firstname, \' \', patronymic, \' \', birthdate, \' (id: \', id, \')\') as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic, ' ', birthdate, ' (id: ', id, ')') as label", 'id as id'])->asArray()->all();

        echo $form->field($model, 'fio2')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $people,
                'select' => new JsExpression("function( event, ui ) {
                    let e1 = document.getElementById('participant_id1');
                    let e2 = document.getElementById('participant_id2');

                    $('#participant_id2').val(ui.item.id);
                    $.get(
                            \"" . Url::toRoute('info') . "\", 
                            {id1: e1.value, id2: e2.value},
                        function(res){
                            let elem = document.getElementById('commonBlock');
                            elem.innerHTML = res;
                        }
                    );
                    CheckFieldsFill();
                 }"),
                
            ],
            'options'=>[
                'class'=>'form-control on',
            ]
        ])->label('ФИО участника деятельности №2');

        echo $form->field($model, 'id2')->hiddenInput(['class' => 'part', 'id' => 'participant_id2', 'name' => 'participant2'])->label(false);

        ?>

    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div id="commonBlock" style="display: none">
        Блок с общей информацией
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="form-group">
        <?= Html::submitButton('Объединить участников деятельности', ['id' => 'sub', 'class' => 'btn btn-primary', 'disabled' => 'disabled']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function CheckFieldsFill()
    {
        let elem1 = document.getElementById('participant_id1');
        let elem2 = document.getElementById('participant_id2');
        console.log(elem1.value);
        console.log(elem2.value);
        console.log(elem1.value && elem2.value);
        if (elem1.value && elem2.value)
        {
            let main = document.getElementById('commonBlock');
            main.style.display = 'block';
            main = document.getElementById('sub');
            main.removeAttribute('disabled');
            main = document.getElementById('mergeparticipantmodel-fio1');
            main.setAttribute('readonly', 'true');
            main = document.getElementById('mergeparticipantmodel-fio2');
            main.setAttribute('readonly', 'true');
        }
    }

</script>