<?php

use app\models\work\ErrorsWork;
use app\models\work\UserWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<?php
$access = [12, 13, 14];
$isMethodist = \app\models\common\AccessLevel::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'access_id', $access])->one();
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <div class="content-container col-xs-8" style="float: left">
        <?php
            $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
            $erros = new ErrorsWork();
            echo $erros->ErrorsElectronicJournalSubsystem($user, 0);    // если второй параметр 0, то выводим все ошибки, если 1, то только критические
        ?>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>