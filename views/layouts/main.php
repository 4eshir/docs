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
    <?php
    $this->head();

    ?>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php

    function check_mobile_device() {
        $mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        // var_dump($agent);exit;
        foreach ($mobile_agent_array as $value) {
            if (strpos($agent, $value) !== false) return true;
        }
        return false;
    }


    NavBar::begin([
        'brandLabel' => 'Главная',
        'brandUrl' => ['/site/index'],
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
                    . Html::beginForm(['/site/logout'], 'post', ['from' => '1'])
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
        if (check_mobile_device())
        {
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [

                    /*['label' => 'Реестр ПО', 'items' => [
                        ['label' => 'Работа с базой ПО', 'url' => ['/as-admin/index']],
                        ['label' => 'Страны', 'url' => ['/as-admin/index-country']],
                        ['label' => 'Тип ПО', 'url' => ['/as-admin/index-as-type']],
                        ['label' => 'Компании', 'url' => ['/as-admin/index-company']],
                        ['label' => 'Вид лицензии', 'url' => ['/as-admin/index-license']],
                    ]],*/
                    ['label' => 'Мат. ценности', 'items' => [
                        ['label' => 'Материальные объекты', 'url' => ['material-object/index']],
                        ['label' => 'Контейнеры', 'url' => ['container/index']],
                    ]],
                    ['label' => 'Эл. журнал', 'items' => [
                        ['label' => 'Учебные группы', 'url' => ['training-group/index']],
                        ['label' => 'Журнал', 'url' => ['journal/index']],
                        ['label' => 'Генерация сертификатов', 'url' => ['certificat/main-index']],
                    ]],
                    ['label' => 'Документооборот', 'items' => [
                        ['label' => 'Исходящая документация', 'url' => ['docs-out/index']],
                        ['label' => 'Входящая документация', 'url' => ['document-in/index']],
                        ['label' => 'Приказы по основной деятельности', 'url' => ['document-order/index', 'c' => 1]],
                        ['label' => 'Приказы по образовательной деятельности', 'url' => ['document-order/index', 'c'=>2]],
                        ['label' => 'Положения, инструкции и правила', 'url' => Url::to(['regulation/index', 'c' => 1])],
                        ['label' => 'Положения о мероприятиях', 'url' => Url::to(['regulation/index', 'c' => 2])],
                        ['label' => 'Мероприятия', 'url' => ['event/index']],
                        ['label' => 'Учет достижений в мероприятиях', 'url' => ['foreign-event/index']],
                        ['label' => 'Образовательные программы', 'url' => ['training-program/index']],
                        ['label' => 'Учет ответственности работников', 'url' => ['local-responsibility/index']],
                    ]],

                    ['label' => 'Дополнительно', 'items' => [
                        ['label' => 'Организации', 'url' => ['/company/index']],
                        ['label' => 'Должности', 'url' => ['/position/index']],
                        ['label' => 'Люди', 'url' => ['/people/index']],
                        ['label' => 'Участники деятельности', 'url' => ['/foreign-event-participants/index']],
                        ['label' => 'Формы мероприятий', 'url' => ['/event-form/index']],
                        ['label' => 'Отчетные мероприятия', 'url' => ['/event-external/index']],
                        ['label' => 'Отделы', 'url' => ['/branch/index']],
                        ['label' => 'Помещения', 'url' => ['/auditorium/index']],
                        ['label' => 'Виды ответственности', 'url' => ['/responsibility-type/index']],
                        ['label' => 'Список пользователей', 'url' => ['/user/index']],
                    ]],
                    Yii::$app->user->identity->getId() == 1 || Yii::$app->user->identity->getId() == 31 ? (
                    ['label' => 'Обратная связь', 'url' => ['/site/feedback-answer']]
                    ) : (
                    ['label' => 'Обратная связь', 'url' => ['/site/feedback']]
                    ),
                    Yii::$app->user->isGuest ? (
                    ['label' => 'Войти', 'url' => ['/site/login']]
                    ) : (
                        '<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Личный кабинет<span class="caret"></span></a>'
                        .'<ul id="w10" class="dropdown-menu">'
                        .'<li><a href="'.Url::to(['/lk/info', 'id' => Yii::$app->user->identity->getId()]).'" tabindex="-1">Личный кабинет ('.Yii::$app->user->identity->username.')</a></li>'
                        .'<li><a href="'.Url::to(['/site/logout', 'from' => '1']).'" tabindex="-1" data-method="POST">Выйти</a></li>'
                        . '</ul>'
                        .'</li>'
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
                    Yii::$app->user->identity->getId() == 1 || Yii::$app->user->identity->getId() == 31 ? (
                    ['label' => 'Обратная связь', 'url' => ['/site/feedback-answer']]
                    ) : (
                    ['label' => 'Обратная связь', 'url' => ['/site/feedback']]
                    ),
                    Yii::$app->user->isGuest ? (
                    ['label' => 'Войти', 'url' => ['/site/login']]
                    ) : (
                        '<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Личный кабинет<span class="caret"></span></a>'
                        .'<ul id="w10" class="dropdown-menu">'
                        .'<li><a href="'.Url::to(['/lk/info', 'id' => Yii::$app->user->identity->getId()]).'" tabindex="-1">Личный кабинет ('.Yii::$app->user->identity->username.')</a></li>'
                        .'<li><a href="'.Url::to(['/site/logout', 'from' => '1']).'" tabindex="-1" data-method="POST">Выйти</a></li>'
                        . '</ul>'
                        .'</li>'
                    )
                ],
            ]);
            NavBar::end();
        }

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
