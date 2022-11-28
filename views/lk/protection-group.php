<?php

use app\models\work\UserWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupWork*/

?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <div class="content-container" style="float: left">
        <h3>Сведения о защите работ</h3>
        <br>
        <div style="margin-left: 15px;">
            <?php
                echo $model->InfoProtectionGroup(Yii::$app->user->identity->getId());
            ?>
        </div>
    </div>

</div>

