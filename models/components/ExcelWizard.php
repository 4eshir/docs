<?php


namespace app\models\components;


use app\models\common\ForeignEventParticipants;
use app\models\common\RussianNames;
use app\models\extended\JournalModel;
use app\models\work\ForeignEventWork;
use app\models\work\LessonThemeWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamWork;
use app\models\work\ThematicPlanWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;
use Yii;

class ExcelWizard
{
    static public function GetSex($name)
    {
        $searchName = RussianNames::find()->where(['name' => $name])->one();
        if ($searchName == null)
            return "Другое";
        if ($searchName->Sex == "М") return "Мужской";
        else return "Женский";
    }

    static public function WriteUtp($filename, $training_program_id)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/program/temp/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/program/temp/'.$filename);
        $index = 2;
        while ($index <= $inputData->getActiveSheet()->getHighestRow() && strlen($inputData->getActiveSheet()->getCellByColumnAndRow(0, $index)->getValue()) > 1)
        {
            $theme = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $index)->getValue();
            $controlId = $inputData->getActiveSheet()->getCellByColumnAndRow(1, $index)->getValue();
            $tp = new ThematicPlanWork();
            $tp->theme = $theme;
            $tp->control_type_id = $controlId;
            $tp->training_program_id = $training_program_id;
            $tp->save();
            $index++;
        }
        unlink(Yii::$app->basePath.'/upload/files/program/temp/'.$filename);
    }

    static public function DownloadKUG($training_group_id)
    {
        ini_set('memory_limit', '512M');
        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Disposition: attachment;filename="kug.xlsx"');
        //header('Cache-Control: max-age=0');

// If you're serving to IE over SSL, then the following may be needed
        //header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        //header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //header ('Pragma: public'); // HTTP/1.0

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/template_KUG.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/template_KUG.xlsx');


        $lessons = LessonThemeWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $training_group_id])
                                        ->orderBy(['trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.lesson_start_time' => SORT_ASC])->all();
        $c = 1;

        foreach ($lessons as $lesson)
        {

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 12 + $c, $c);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, 12 + $c, $lesson->trainingGroupLesson->lesson_date);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 12 + $c, mb_substr($lesson->trainingGroupLesson->lesson_start_time, 0, -3).' - '.mb_substr($lesson->trainingGroupLesson->lesson_end_time, 0, -3));

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 12 + $c, $lesson->theme);
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(4, 12 + $c, $lesson->trainingGroupLesson->duration);
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(5, 12 + $c, "Групповая");
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(6, 12 + $c, $lesson->controlType->name);
            $c++;
        }



        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=kug.xls");
//header("Content-Disposition: attachment;filename=test.xls");
        header("Content-Transfer-Encoding: binary ");
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel5');
        $writer->save('php://output');
    }

    static public function DownloadJournal($group_id)
    {
        $onPage = 20; //количество занятий на одной странице
        $counter = 0; //основной счетчик для visits
        $lesCount = 0; //счетчик для страниц
        ini_set('memory_limit', '512M');

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/template_JOU.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/template_JOU.xlsx');

        $model = new JournalModel($group_id);

        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
        $newLessons = array();
        foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
        $visits = VisitWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'foreignEventParticipant.firstname' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.id' => SORT_ASC])->all();

        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;

        $parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
        $lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();


        while ($lesCount < count($lessons) / $onPage)
        {
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, (count($parts) + 2) * $lesCount + 1, 'ФИО/Занятие');

            for ($i = 0; $i < $onPage; $i++) //цикл заполнения дат на странице
            {
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(1 + $i, (count($parts) + 2) * $lesCount + 1, date("d.m", strtotime($lessons[$i]->lesson_date)));
                $inputData->getActiveSheet()->getCellByColumnAndRow(1 + $i, (count($parts) + 2) * $lesCount + 1)->setValueExplicit(date("d.m", strtotime($lessons[$i]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                $inputData->getActiveSheet()->getCellByColumnAndRow(1 + $i, (count($parts) + 2) * $lesCount + 1)->getStyle()->getAlignment()->setTextRotation(90);
                $inputData->getActiveSheet()->getColumnDimensionByColumn(1 + $i)->setWidth('3');
            }

            for($i = 0; $i < count($parts); $i++) //цикл заполнения детей на странице
            {
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $i + (count($parts) * $lesCount) + 2, $parts[$i]->participantWork->shortName);
            }

            $lesCount++;
        }

        /*$row = 1;


        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'ФИО/Занятие');
        $c = 0;
        for ($i = $lesCount * $onPage; $i < count($lessons) && $i < ($lesCount + 1) * $onPage; $i++)
        {
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1 + $c, $row, date("d.m", strtotime($lessons[$i]->lesson_date)));
            $inputData->getActiveSheet()->getCellByColumnAndRow(1 + $c, $row)->setValueExplicit(date("d.m", strtotime($lessons[$i]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
            $inputData->getActiveSheet()->getCellByColumnAndRow(1 + $c, $row)->getStyle()->getAlignment()->setTextRotation(90);
            $inputData->getActiveSheet()->getColumnDimensionByColumn(1 + $c)->setWidth('3');
            $c++;
        }


        $row++;
        $tempRow = $row;
        foreach ($parts as $part)
        {
            $col = 0;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $part->participantWork->shortName);

            $i = 0;
            while ($i < count($lessons) / count($parts))
            {
                for ($k = 0; $k < $onPage; $k++)
                {
                    //$visits = \app\models\work\VisitWork::find()->where(['training_group_lesson_id' => $lesson->id])->andWhere(['foreign_event_participant_id' => $part->participant->id])->one();

                    $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$counter]])->one();
                    $inputData->getActiveSheet()->setCellValueByColumnAndRow(1 + $col, $row, $visits->excelStatus);
                    $col++;
                    $counter++;
                    $i++;
                }

                $row = $row + count($parts) + 7;
            }
            $row = $tempRow + 1;
        }

        $row = $row + 2;
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'ФИО');
        $row = $row + 2;
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'Подпись');
        $row = $row + 3;
        $lesCount++;
        */


        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=journal.xls");
//header("Content-Disposition: attachment;filename=test.xls");
        header("Content-Transfer-Encoding: binary ");
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel5');
        $writer->save('php://output');
    }

    static public function DownloadEffectiveContract($start_date, $end_date, $budget)
    {
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_EC.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_EC.xlsx');
        //var_dump($inputData);

        //Получаем количество учеников
        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])
            ->andWhere(['IN', 'budget', $budget])
            ->all();

        $tgIds = [];
        foreach ($trainingGroups as $trainingGroup) $tgIds[] = $trainingGroup->id;
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->all();

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 5, count($participants));
        //----------------------------

        //Получаем мероприятия с выбранными учениками

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;
        $eventParticipants = TeacherParticipantWork::find()->where(['IN', 'participant_id', $pIds])->all();

        $eIds = [];
        foreach ($eventParticipants as $eventParticipant) $eIds[] = $eventParticipant->foreign_event_id;

        $eIds2 = [];
        foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

        $events = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date]);

        //-------------------------------------------

        //Международные победители и призеры

        $events1 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => 8])->all();

        $counter1 = 0;
        $counter2 = 0;
        $counterPart1 = 0;
        $allTeams = 0;
        foreach ($events1 as $event)
        {
            $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();
            $tIds = [];
            $teamName = '';
            $counterTeamWinners = 0;
            $counterTeamPrizes = 0;
            $counterTeam = 0;
            foreach ($teams as $team)
            {
                if ($teamName != $team->name)
                {
                    $teamName = $team->name;
                    $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                    if ($res !== null) $counterTeamWinners++;
                    else $counterTeamPrizes++;
                    $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                }
                $tIds[] = $team;
            }

            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
            $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();


            $counter1 += count($achieves1) + $counterTeamPrizes;
            $counter2 += count($achieves2) + $counterTeamWinners;
            $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;
            $allTeams += $counterTeam;

        }

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 6, $counter1);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 7, $counter2);

        //----------------------------------

        //Всероссийские победители и призеры

        $events1 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => 7])->all();

        $counter1 = 0;
        $counter2 = 0;
        $counterPart1 = 0;
        $allTeams = 0;
        foreach ($events1 as $event)
        {
            $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();
            $tIds = [];
            $teamName = '';
            $counterTeamWinners = 0;
            $counterTeamPrizes = 0;
            $counterTeam = 0;
            foreach ($teams as $team)
            {
                if ($teamName != $team->name)
                {
                    $teamName = $team->name;
                    $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                    if ($res !== null) $counterTeamWinners++;
                    else $counterTeamPrizes++;
                    $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                }
                $tIds[] = $team;
            }

            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
            $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();


            $counter1 += count($achieves1) + $counterTeamPrizes;
            $counter2 += count($achieves2) + $counterTeamWinners;
            $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;
            $allTeams += $counterTeam;

        }

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 8, $counter1);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 9, $counter2);

        //----------------------------------

        //Всероссийские победители и призеры

        $events1 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => 6])->all();

        $counter1 = 0;
        $counter2 = 0;
        $counterPart1 = 0;
        $allTeams = 0;
        foreach ($events1 as $event)
        {
            $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->all();
            $tIds = [];
            $teamName = '';
            $counterTeamWinners = 0;
            $counterTeamPrizes = 0;
            $counterTeam = 0;
            foreach ($teams as $team)
            {
                if ($teamName != $team->name)
                {
                    $teamName = $team->name;
                    $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                    if ($res !== null) $counterTeamWinners++;
                    else $counterTeamPrizes++;
                    $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                }
                $tIds[] = $team;
            }

            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $eIds2])->all();
            $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $eIds2])->all();


            $counter1 += count($achieves1) + $counterTeamPrizes;
            $counter2 += count($achieves2) + $counterTeamWinners;
            $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;
            $allTeams += $counterTeam;

        }

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 10, $counter1);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 11, $counter2);

        //----------------------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
    }

    static public function WriteAllCertNumbers($filename, $training_group_id)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $index = 2;
        while ($index < $inputData->getActiveSheet()->getHighestRow() && strlen($inputData->getActiveSheet()->getCellByColumnAndRow(2, $index)->getValue()) > 5)
        {
            $fio = $inputData->getActiveSheet()->getCellByColumnAndRow(2, $index)->getValue();
            $fio = explode(" ", $fio);
            if (count($fio) > 1)
            {
                $people = null;
                if (count($fio) == 2)
                {
                    $people = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $training_group_id])
                        ->andWhere(['participant.secondname' => $fio[0]])->andWhere(['participant.firstname' => $fio[1]])->one();

                }
                if (count($fio) == 3)
                {
                    $people = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $training_group_id])
                        ->andWhere(['participant.secondname' => $fio[0]])->andWhere(['participant.firstname' => $fio[1]])->andWhere(['participant.patronymic' => $fio[2]])->one();
                }
                if ($people !== null)
                {
                    $people->certificat_number = strval($inputData->getActiveSheet()->getCellByColumnAndRow(3, $index)->getValue());
                    $people->save();
                }
                $index++;
            }
        }
    }

    static public function GetAllParticipants($filename)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $splitName = explode(".", $filename);
        $newFilename = $splitName[0].'_new'.'.xls';//.$splitName[1];
        $inputData = $writer->save(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$newFilename);
        $newReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $inputData = $newReader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$newFilename);

        $startRow = 1;

        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $startRow)->getValue();
        while ($startRow < 100 && strlen($tempValue) < 4)
        {
            $startRow++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $startRow)->getValue();
        }
        $fioColumnIndex = 0;
        $tempValue = '_';
        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($fioColumnIndex, $startRow)->getValue();

        while ($fioColumnIndex < 100 && $tempValue !== 'Фамилия Имя Отчество проектанта')
        {
            $fioColumnIndex++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($fioColumnIndex, $startRow)->getValue();
        }

        $birthdateColumnIndex = 0;
        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($birthdateColumnIndex, $startRow)->getValue();
        while ($birthdateColumnIndex < 100 && $tempValue !== 'Дата рождения (л)')
        {
            $birthdateColumnIndex++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($birthdateColumnIndex, $startRow)->getValue();
        }
        $names = [];
        $curName = "_";
        $startIndex = $startRow + 1;
        $mainIndex = 0;

        while ($mainIndex < $inputData->getActiveSheet()->getHighestRow() - $startRow)
        {
            $curName = $inputData->getActiveSheet()->getCellByColumnAndRow($fioColumnIndex, $startIndex + $mainIndex)->getValue();
            if ($curName !== null)
                $names[] = $curName;
            else
                $names[] = "none none none";
            $mainIndex++;
        }

        $birthdates = [];
        $curDate = "_";
        $startIndex = $startRow + 1;
        $mainIndex = 0;
        while ($mainIndex < $inputData->getActiveSheet()->getHighestRow() - $startRow)
        {
            $curDate = $inputData->getActiveSheet()->getCellByColumnAndRow($birthdateColumnIndex, $startIndex + $mainIndex)->getFormattedValue();
            $birthdates[] = $curDate;
            $mainIndex++;
        }
        //unset($birthdates[count($birthdates) - 1]);
        //unset($names[count($names) - 1]);

        $participants = array();
        for ($i = 0; $i != count($names); $i++)
        {
            $fio = explode(" ", $names[$i]);
            if (count($fio) == 3)
                $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['patronymic' => $fio[2]])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
            else {
                if (count($fio) > 3)
                {
                    $patr = '';
                    for ($j = 2; $j != count($fio); $j++)
                        $patr .= $fio[$j].' ';
                    $patr = mb_substr($patr, 0, -1);
                    $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['patronymic' => $patr])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
                }
                else
                    $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
            }
            if ($newParticipant == null)
            {
                $newParticipant = new ForeignEventParticipants();
                $newParticipant->firstname = $fio[1];
                $newParticipant->secondname = $fio[0];
                if (count($fio) == 3)
                    $newParticipant->patronymic = $fio[2];
                if (count($fio) > 3)
                {
                    $patr = '';
                    for ($j = 2; $j != count($fio); $j++)
                        $patr .= $fio[$j].' ';
                    $patr = mb_substr($patr, 0, -1);
                    $newParticipant->patronymic = $patr;
                }
                $newParticipant->birthdate = date("Y-m-d", strtotime($birthdates[$i]));
                $newParticipant->sex = self::GetSex($fio[1]);
                $newParticipant->save();
            }
            $participants[] = $newParticipant;
        }
        return $participants;
    }

}