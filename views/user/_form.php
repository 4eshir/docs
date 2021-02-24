<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'firstname')->textInput() ?>
    <?= $form->field($model, 'secondname')->textInput() ?>
    <?= $form->field($model, 'patronymic')->textInput() ?>
    <?= $form->field($model, 'username')->textInput() ?>

    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 1])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'addUsers')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 2])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewRoles')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 3])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editRoles')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 4])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewOut')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 5])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editOut')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 6])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewIn')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 7])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editIn')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 8])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewOrder')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 9])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editOrder')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 10])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewRegulation')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 11])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editRegulation')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 12])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewEvent')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 13])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editEvent')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 14])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewAS')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 15])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editAS')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 16])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewAdd')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 17])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editAdd')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 18])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewForeign')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 19])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editForeign')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 20])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'viewProgram')->checkbox(['checked' => $value]) ?>
    <?php
    $tmp = \app\models\common\AccessLevel::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 21])->one();
    $value = 0;
    if ($tmp != null) $value = true; else $value = false;
    ?>
    <?= $form->field($model, 'editProgram')->checkbox(['checked' => $value]) ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
