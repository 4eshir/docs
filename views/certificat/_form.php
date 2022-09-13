<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\work\TrainingGroupParticipantWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\CertificatWork */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
if(isset($_GET['group_id'])) {
    $model->group_id = $_GET['group_id'];
}
?>

<div class="certificat-form">

    <?php $form = ActiveForm::begin([
            'options' => ['target' => '_blank', 'id' => 'form1']
    ]); ?>


    <?php
    $templates = \app\models\work\CertificatTemplatesWork::find()->orderBy(['id' => SORT_DESC])->all();
    $items = \yii\helpers\ArrayHelper::map($templates,'id','name');
    $params = [];

    echo $form->field($model, 'certificat_template_id')->dropDownList($items,$params)->label('Шаблон сертификатов');

    ?>



    <?php
    $date = date("Y-m-d", strtotime('+3 days'));
    //$groups = \app\models\work\TrainingGroupWork::find()->where(['archive' => 0])->orderBy(['id' => SORT_DESC])->all();
    $groups = \app\models\work\TrainingGroupWork::find()->where(['<=','finish_date', $date])->andWhere(['archive' => 0])->orderBy(['id' => SORT_DESC])->all();
    $items = \yii\helpers\ArrayHelper::map($groups,'id','number');
    $params = [
        'prompt' => '---',
        'id' => 'groupList',
        'onchange' => 'changeGroup()',
    ];

    echo $form->field($model, 'group_id')->dropDownList($items,$params)->label('Группа');

    ?>

    <?php

    $cert = \app\models\work\CertificatWork::find()->all();

    $cIds = [];
    foreach($cert as $one) $cIds[] = $one->training_group_participant_id;

    $tps = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['trainingGroup.archive' => 0])->andWhere(['status' => 0])->andWhere(['NOT IN', 'training_group_participant.id', $cIds])->all();

    echo '<table class="table table-striped">';
    foreach($tps as $tp)
    {
        echo '<tr>';
        $style = '';
        if ($model->group_id != $tp->training_group_id)
            $style = '" style="display: none"';
        echo '<td class="parts '.$tp->training_group_id.$style.'>'.$form->field($model, 'participant_id[]')->checkbox(['label' => $tp->participantWork->fullName, 'value' => $tp->id])->label(false).'</td>';

        echo '</tr>';
    }
    echo '</table>';

    ?>

    <div class="form-group">
        <?php
            echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
            //echo Html::a('Сохранить', \yii\helpers\Url::to(['certificat/download']), ['class' => 'btn btn-success', 'style' => 'target="_blank"']);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    function changeGroup()
    {
        let elem = document.getElementById('groupList');
        let parts = document.getElementsByClassName('parts');
        for (let i = 0; i < parts.length; i++)
            parts[i].style.display = 'none';

        parts = document.getElementsByClassName(elem.value);
        for (let i = 0; i < parts.length; i++)
            parts[i].style.display = 'block';
    }

    document.getElementById("form1").onsubmit = function()
    {
        //window.open("https://google.ru", '_blank');
        //window.location.href = "https://docs/index.php?r=certificat/index";
        setTimeout(redirectHandler, 500);
    }

    function redirectHandler()
    {
        window.location = "https://index.schooltech.ru/docs/web/index.php?r=certificat/index";
    } 
</script>