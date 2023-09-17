<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'ЦСХД';

?>

<script>
    $(window).resize(function () {
        alert("window.innerWidth");
    });
</script>

<style>

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .portfolio-wrap {
        max-width: 1120px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    .portfolio-item {
        padding: 10px;
    }
    .portfolio-item a {
        display: block;
        text-decoration: none;
        color: white;
    }
    .portfolio-item-wrap {
        position: relative;
        overflow: hidden;
        text-align: center;
        box-shadow: 0 0 5px rgba(0, 0, 0, .2);
        background: black;
        color: white;
        border-radius: 10px;
    }
    .portfolio-item img {
        display: block;
        width: 100%;
        opacity: .75;
        transition: .5s ease-in-out;
    }
    .portfolio-item-inner {
        position: absolute;
        top: 45%;
        left: 7%;
        right: 7%;
        bottom: 45%;
        border: 1px solid white;
        border-width: 0 1px 1px;
        transition: .4s ease-in-out;
    }
    .portfolio-heading {
        overflow: hidden;
        transform: translateY(-50%);
    }
    .portfolio-heading h3 {
        font-family: 'Century Gothic', sans-serif;
        font-weight: normal;
        display: table;
        margin: 0 auto;
        padding: 0 10px 20px 10px;
        position: relative;
    }
    .portfolio-heading h3:before, .portfolio-heading h3:after {
        content: "";
        position: absolute;
        top: 50%;
        width: 50px;
        height: 1px;
        background: white;
    }
    .portfolio-heading h3:before {
        left: -50px;
    }
    .portfolio-heading h3:after {
        right: -50px;
    }
    .portfolio-item-inner ul {
        position: absolute;
        top: 50%;
        width: 100%;
        transform: translateY(-50%);
        padding: 0 20px;
        opacity: 0;
        list-style: none;
        font-family: 'Raleway', sans-serif;
        transition: .4s ease-in-out;
    }
    .portfolio-item-inner li {
        position: relative;
        font-size: 16px;
        padding: 2px 0;
        margin-bottom: 4px;
    }

    .portfolio-item-inner li:last-child:after {
        content: none;
    }
    .portfolio-item:hover img {
        opacity: 0.45;
        transform: scale(1.1);
    }
    .portfolio-item:hover .portfolio-item-inner {
        top: 7%;
        bottom: 7%;
    }
    .portfolio-item:hover ul {
        opacity: 1;
        transition-delay: .5s;
    }
    @media (min-width: 700px) {
        .portfolio-item {
            flex-basis: 80%;
            flex-shrink: 0;
        }
    }
    @media (min-width: 1200px) {
        .portfolio-item {
            flex-basis: 33.333333333%;
        }
    }
</style>

<?php

$img_src = "item.jpg";
$img_src_main = "main.jpg";

?>

<div class="site-index" style="z-index: 1; position: absolute">

    <?php function check_mobile_device1() {
        $mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        // var_dump($agent);exit;
        foreach ($mobile_agent_array as $value) {
            if (strpos($agent, $value) !== false) return true;
        }
        return false;
    } ?>
    <?php if (!check_mobile_device1()) {?>
    <div class="portfolio-wrap">
        <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Документооборот</b></h3>
                        </div>
                        <ul>
                            <li><?php echo Html::a('Исходящая документация', \yii\helpers\Url::to(['docs-out/index'])) ?></li>
                            <li><?php echo Html::a('Входящая документация', \yii\helpers\Url::to(['document-in/index'])) ?></li>
                            <li><?php echo Html::a('Приказы по осн. деятельности', \yii\helpers\Url::to(['document-order/index', 'c' => 1])) ?></li>
                            <li><?php echo Html::a('Приказы по обр. деятельности', \yii\helpers\Url::to(['document-order/index', 'c' => 2])) ?></li>
                            <li><?php echo Html::a('Положения, инструкции, правила', \yii\helpers\Url::to(['regulation/index', 'c' => 1])) ?></li>
                            <li><?php echo Html::a('Положения о мероприятиях', \yii\helpers\Url::to(['regulation/index', 'c' => 2])) ?></li>
                            <li><?php echo Html::a('Мероприятия', \yii\helpers\Url::to(['event/index'])) ?></li>
                            <li><?php echo Html::a('Учет достижений в мероприятиях', \yii\helpers\Url::to(['foreign-event/index'])) ?></li>
                            <li><?php echo Html::a('Образовательные программы', \yii\helpers\Url::to(['training-program/index'])) ?></li>
                            <li><?php echo Html::a('Учет ответственности работников', \yii\helpers\Url::to(['local-responsibility/index'])) ?></li>
                        </ul>
                    </div>
                </a>
            </div>
        </div>

        <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Эл. журнал</b></h3>
                        </div>
                        <ul>
                            <li><?php echo Html::a('Учебные группы', \yii\helpers\Url::to(['training-group/index'])) ?></li>
                            <li><?php echo Html::a('Журнал', \yii\helpers\Url::to(['journal/index'])) ?></li>
                            <li><?php echo Html::a('Генерация сертификатов', \yii\helpers\Url::to(['certificat/main-index'])) ?></li>
                        </ul>
                    </div>
                </a>
            </div>
        </div>

        <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Справочники</b></h3>
                        </div>
                        <ul>
                            <li><?php echo Html::a('Организации/Должности/Люди', \yii\helpers\Url::to(['dictionaries/service'])) ?></li>
                            <!-- <li><?php echo Html::a('Организации', \yii\helpers\Url::to(['company/index'])) ?></li>
                            <li><?php echo Html::a('Должности', \yii\helpers\Url::to(['position/index'])) ?></li>
                            <li><?php echo Html::a('Люди', \yii\helpers\Url::to(['people/index'])) ?></li> -->
                            <li><?php echo Html::a('Учебная деят./Мероприятия', \yii\helpers\Url::to(['dictionaries/studies'])) ?></li>
                            <!-- <li><?php echo Html::a('Участники деятельности', \yii\helpers\Url::to(['foreign-event-participants/index'])) ?></li>
                            <li><?php echo Html::a('Формы мероприятий', \yii\helpers\Url::to(['event-form/index'])) ?></li>
                            <li><?php echo Html::a('Отчетные мероприятия', \yii\helpers\Url::to(['event-external/index'])) ?></li> -->
                            <li><?php echo Html::a('Отделы/Помещения/Ответст.', \yii\helpers\Url::to(['dictionaries/premises'])) ?></li>
                            <!-- <li><?php echo Html::a('Отделы', \yii\helpers\Url::to(['branch/index'])) ?></li>
                            <li><?php echo Html::a('Помещения', \yii\helpers\Url::to(['auditorium/index'])) ?></li>
                            <li><?php echo Html::a('Виды ответственности', \yii\helpers\Url::to(['responsibility-type/index'])) ?></li> -->
                            <li><?php echo Html::a('Пользователи/Роли', \yii\helpers\Url::to(['dictionaries/users'])) ?></li>
                            <!-- <li><?php echo Html::a('Список пользователей', \yii\helpers\Url::to(['user/index'])) ?></li>
                            <li><?php echo Html::a('Роли', \yii\helpers\Url::to(['role/index'])) ?></li> -->
                        </ul>
                    </div>
                </a>
            </div>
        </div>

        <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Что нового?</b></h3>
                        </div>
                        <ul>
                            <li><?php echo Html::a('Список изменений', \yii\helpers\Url::to(['patchnotes/index'])) ?></li>
                            <?php
                            if (Yii::$app->user->identity !== null)
                                if (Yii::$app->user->identity->getId() === 1 || Yii::$app->user->identity->getId() === 31)
                                    echo '<li>'.Html::a('Добавить патчноут', \yii\helpers\Url::to(['patchnotes/add-note'])).'</li>';
                            ?>
                        </ul>
                    </div>
                </a>
            </div>
        </div>

        <!-- <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Реестр ПО</b></h3>
                        </div>
                        <ul>
                            <li><?php echo Html::a('База ПО', \yii\helpers\Url::to(['as-admin/index'])) ?></li>
                            <li><?php echo Html::a('Страны', \yii\helpers\Url::to(['as-admin/index-country'])) ?></li>
                            <li><?php echo Html::a('Типы ПО', \yii\helpers\Url::to(['as-admin/index-as-type'])) ?></li>
                            <li><?php echo Html::a('Компании', \yii\helpers\Url::to(['as-admin/index-company'])) ?></li>
                            <li><?php echo Html::a('Виды лицензий', \yii\helpers\Url::to(['as-admin/index-license'])) ?></li>
                        </ul>
                    </div>
                </a>
            </div>
        </div> -->

        <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Мат. ценности</b></h3>
                        </div>
                        <ul>
                            <li><?php echo Html::a('Договоры', \yii\helpers\Url::to(['contract/index'])) ?></li>
                            <li><?php echo Html::a('Документы о поступлении', \yii\helpers\Url::to(['invoice/index'])) ?></li>
                            <li><?php echo Html::a('Материальные объекты', \yii\helpers\Url::to(['material-object/index'])) ?></li>
                            <li><?php echo Html::a('Контейнеры', \yii\helpers\Url::to(['container/index'])) ?></li>
                            <li><?php echo Html::a('Бух. объединения', \yii\helpers\Url::to(['product-union/index'])) ?></li>
                            <?php
                            if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)) {
                                echo '<li>' . Html::a('Классы', \yii\helpers\Url::to(['kind-object/index'])) . '</li>';
                            } ?>
                        </ul>
                    </div>
                </a>
            </div>
        </div>

        <div class="portfolio-item">
            <div class="portfolio-item-wrap">
                <a href="">
                    <img src="<?php echo $img_src; ?>">
                    <div class="portfolio-item-inner">
                        <div class="portfolio-heading">
                            <h3><b>Отчеты</b></h3>
                        </div>
                        <ul>
                            <?php
                                    echo '<li>'.Html::a('Отчеты по обучающимся', \yii\helpers\Url::to(['report/man-hours-report'])).'</li>';
                                    echo '<li>'.Html::a('Отчеты по мероприятиям', \yii\helpers\Url::to(['report/foreign-event-report'])).'</li>';
                                    echo '<li>'.Html::a('Отчеты по готовым формам', \yii\helpers\Url::to(['report-form/index'])).'</li>';
                                    //echo '<li>'.Html::a('Полезные функции', \yii\helpers\Url::to(['report/useful-side-report'])).'</li>';
                            ?>
                        </ul>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <?php } else { echo '<h3 style="text-align: center"><b>Добро пожаловать в систему!</b></h3><br><h4 style="text-align: center">Для навигации используйте меню вверху экрана</h4>';}?>

</div>
<div>
    <img src="<?php echo $img_src_main; ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0"/>
</div>
<div>
    <!--<img src="new_year_ball.png" style="position: absolute; top: 100px; left: 50px; width: 250px; height: 250px; z-index: 0"/>-->
</div>