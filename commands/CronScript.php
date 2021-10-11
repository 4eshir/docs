<?php

use app\models\work\GroupErrorsWork;
use app\models\work\ProgramErrorsWork;
?>

<?php
// запускаем на проверку все группы
/*$errorsGroupCheck = new GroupErrorsWork();
$groups = \app\models\common\TrainingGroupWork::find()->all();
foreach ($groups as $group)
{
    $errorsGroupCheck->CheckErrorsTrainingGroup($group->id);
}*/

// запускаем на проверку все образовательные программы
$programs = \app\models\work\TrainingProgramWork::find()->all();
foreach ($programs as $program)
{
    $errorsProgramCheck = new ProgramErrorsWork();
    $errorsProgramCheck->CheckErrorsTrainingProgram($program->id);
}
?>

