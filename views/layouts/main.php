<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Главная',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    if (Yii::$app->user->isGuest)
    {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [

                Yii::$app->user->isGuest ? (
                ['label' => 'Войти', 'url' => ['/site/login']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Выйти (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();
    }
    else
    {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [

                ['label' => 'Реестр ПО', 'items' => [
                    ['label' => 'Работа с базой ПО', 'url' => ['/as-admin/index']],
                    ['label' => 'Страны', 'url' => ['/as-admin/index-country']],
                    ['label' => 'Тип ПО', 'url' => ['/as-admin/index-as-type']],
                    ['label' => 'Вид лицензии', 'url' => ['/as-admin/index-license']],
                ]],
                ['label' => 'Документооборот', 'items' => [
                    ['label' => 'Исходящая документация', 'url' => ['docs-out/index']],
                    ['label' => 'Входящая документация', 'url' => ['document-in/index']],
                    ['label' => 'Приказы', 'url' => ['document-order/index']],
                    ['label' => 'Положения, инструкции и правила', 'url' => Url::to(['regulation/index', 'c' => 1])],
                    ['label' => 'Положения о мероприятиях', 'url' => Url::to(['regulation/index', 'c' => 2])],
                    ['label' => 'Мероприятия', 'url' => ['event/index']],
                    ['label' => 'Участие во внешних мероприятиях', 'url' => ['foreign-event/index']],
                ]],

                ['label' => 'Дополнительно', 'items' => [
                    ['label' => 'Организации', 'url' => ['/company/index']],
                    ['label' => 'Должности', 'url' => ['/position/index']],
                    ['label' => 'Люди', 'url' => ['/people/index']],
                    ['label' => 'Участники', 'url' => ['/foreign-event-participants/index']],
                    ['label' => 'Формы мероприятий', 'url' => ['/event-form/index']],
                    ['label' => 'Отчетные мероприятия', 'url' => ['/event-external/index']],
                    ['label' => 'Участники мероприятий', 'url' => ['/foreign-event-participants/index']],
                ]],
                ['label' => 'Пользователи', 'items' => [
                    ['label' => 'Список пользователей', 'url' => ['/user/index']],
                    ['label' => 'Сменить пароль', 'url' => ['/user/change-password', 'id' => Yii::$app->user->identity->getId()]]
                ]],
                ['label' => 'Обратная связь', 'url' => ['/site/feedback']],
                Yii::$app->user->isGuest ? (
                ['label' => 'Войти', 'url' => ['/site/login']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Выйти (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();

    }
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; "ЦСХД" ГАОУ АО ДО "РШТ" <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
