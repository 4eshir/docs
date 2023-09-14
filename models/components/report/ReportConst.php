<?php

namespace app\models\components\report;

class ReportConst
{
    const PROD = 0; // боевой режим запуска функции
    const TEST = 1; // тестовый режим запуска функции
    const COMMERCIAL = 0; // коммерческие группы
    const BUDGET = 1; // бюджетные группы
    const BUDGET_ALL = [0, 1]; // все группы по типам бюджета

    const AGES_ALL = 0; // переменная для отключения выборки по возрасту
    const AGES_ALL_18 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]; // список для всех возрастов до 18 лет


    const START_EARLY_END_IN = 1; // выборка групп, начавших обучение РАНЕЕ заданного периода и завершившие обучение В заданный период
    const START_IN_END_LATER = 2; // выборка групп, начавших обучение В заданный период и завершившие обучение ПОСЛЕ заданного периода
    const START_IN_END_IN = 3; // выборка групп, начавших обучение В заданный период и завершившие обучение В заданный период
    const START_EARLY_END_LATER = 4; // выборка групп, начавших обучение РАНЕЕ заданного периода и завершившие обучение ПОСЛЕ заданного периода
    const ALL_DATE_SELECTION = [ReportConst::START_EARLY_END_IN,
                                ReportConst::START_IN_END_LATER,
                                ReportConst::START_IN_END_IN,
                                ReportConst::START_EARLY_END_LATER]; // выборка по всем типам

    const MALE = "Мужской"; // пол - мужской
    const FEMALE = "Женский"; // пол - женский

    const NETWORK = 1; // сетевая форма обучения группы
    const NOT_NETWORK = 0; // очная форма обучения группы
}