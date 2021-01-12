<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?php $message = 'У Вас нет прав для выполнения этого действия!'; ?>
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Обратитесь к администратору системы или вернитесь на страницу назад.
    </p>

</div>
