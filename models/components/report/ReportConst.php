<?php

namespace app\models\components\report;

class ReportConst
{
    const BRANCHES = [1, 2, 3, 4, 5, 7, 8]; // Все отделы организации
    const EVENT_LEVELS = [3, 4, 5, 6, 7, 8]; // Все уровни мероприятий
    const FOCUSES = [1, 2, 3, 4, 5]; // Все направленности (для мероприятий)
    const ALLOW_REMOTES = [1, 2]; // Все формы реализации (для мероприятий)

    const PROD = 0; // боевой режим запуска функции
    const TEST = 1; // тестовый режим запуска функции
}