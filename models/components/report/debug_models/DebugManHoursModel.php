<?php

namespace app\models\components\report\debug_models;

class DebugManHoursModel
{
    public $group; // название группы
    public $lessonsChangeTeacher; // занятия выбранного педагога в группе
    public $lessonsAll; // все занятия группы
    public $participants; // обучающиеся группы
    public $manHours; // человеко-часы группы
}