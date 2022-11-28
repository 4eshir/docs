<?php
//require __DIR__ . '/../models/work/ProgramErrorsWork.php';
?>

<?php
// запускаем на проверку все группы
/*$errorsGroupCheck = new GroupErrorsWork();
$groups = \app\models\common\TrainingGroupWork::find()->all();
foreach ($groups as $group)
{
    $errorsGroupCheck->CheckErrorsTrainingGroup($group->id);
}*/

$text = "Check ";

$fp = fopen(__DIR__.'/log.txt','a');

// записываем данные в открытый файл
fwrite($fp, $text);

//не забываем закрыть файл, это ВАЖНО
fclose($fp);

// запускаем на проверку все образовательные программы
$programs = \app\models\work\TrainingProgramWork::find()->all();
foreach ($programs as $program)
{
    $errorsProgramCheck = new ProgramErrorsWork();
    $errorsProgramCheck->CheckErrorsTrainingProgram($program->id);

}



?>

