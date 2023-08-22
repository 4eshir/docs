<?php

namespace app\models\components\report;

class ReportConst
{
    const PROD = 0; // боевой режим запуска функции
    const TEST = 1; // тестовый режим запуска функции
    const COMMERCIAL = 0;
    const BUDGET = 1;
    const BUDGET_ALL = [0, 1];
    const AGES_ALL = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];
}