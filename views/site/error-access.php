<div class="alert alert-danger">
    <?php use yii\helpers\Html;

    $message = 'У Вас нет прав для выполнения этого действия!'; ?>
    <?= nl2br(Html::encode($message)) ?>
</div>

<p>
    Обратитесь к администратору системы или вернитесь на страницу назад.
</p>