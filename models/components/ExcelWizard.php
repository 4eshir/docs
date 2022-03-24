<?php

namespace app\models\components;


use app\models\common\ForeignEventParticipants;
use app\models\common\RussianNames;
use app\models\extended\JournalModel;
use app\models\work\DocumentOrderWork;
use app\models\work\ForeignEventWork;
use app\models\work\LessonThemeWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeamWork;
use app\models\work\ThematicPlanWork;
use app\models\work\BranchProgramWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

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
        exit;
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

        $magic = 8; //  смещение между страницами засчет фио+подписи и пустых строк
        while ($lesCount < count($lessons) / $onPage)
        {
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, (count($parts) + $magic) * $lesCount + 1, 'ФИО/Занятие');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, (count($parts) + $magic) * $lesCount + 1 + count($parts) + 3, 'ФИО');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, (count($parts) + $magic) * $lesCount + 1 + count($parts) + 5, 'Подпись');

            for ($i = 0; $i + $lesCount * $onPage < count($lessons) && $i < $onPage; $i++) //цикл заполнения дат на странице
            {
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(1 + $i, (count($parts) + $magic) * $lesCount + 1, date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)));
                $inputData->getActiveSheet()->getCellByColumnAndRow(1 + $i, (count($parts) + $magic) * $lesCount + 1)->setValueExplicit(date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                $inputData->getActiveSheet()->getCellByColumnAndRow(1 + $i, (count($parts) + $magic) * $lesCount + 1)->getStyle()->getAlignment()->setTextRotation(90);
                $inputData->getActiveSheet()->getColumnDimensionByColumn(1 + $i)->setWidth('3');
            }

            for($i = 0; $i < count($parts); $i++) //цикл заполнения детей на странице
            {
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $i + ((count($parts) + $magic) * $lesCount) + 2, $parts[$i]->participantWork->shortName);
            }

            $lesCount++;
        }

        $delay = 0;
        for ($cp = 0; $cp < count($parts); $cp++)
        {
            $pages = 0;
            for ($i = 0; $i < count($lessons); $i++, $delay++)
            {
                $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$delay]])->one();
                if ($i % $onPage === 0 && $i !== 0) { $pages++; }
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(1 + $i % $onPage, 2 + $cp + $pages * (count($parts) + $magic), $visits->excelStatus);
            }
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

    //получить всех участников заданного отдела мероприятий в заданный период
    static public function GetAllParticipantsForeignEvents($event_level, $events_id, $events_id2, $start_date, $end_date, $branch_id, $focus_id)
    {
        if ($events_id == 0)
            $events1 = ForeignEventWork::find()->where(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->all();
        else
            $events1 = ForeignEventWork::find()->where(['IN', 'id', $events_id])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->all();


        $partsLink = null;
        $pIds = [];
        $eIds = [];
        if ($branch_id !== 0)
        {
            
            foreach ($events1 as $event) $eIds[] = $event->id;

            if ($focus_id !== 0)
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->andWhere(['teacherParticipant.focus' => $focus_id])->all();
            else
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->all();

            foreach ($partsLink as $part) $pIds[] = $part->teacherParticipant->participant_id;
        }



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
                    if ($partsLink !== null)
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'participant_id', $pIds])->one();
                    else
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                }
                $tIds[] = $team;
            }

            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            //var_dump(TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $event->id])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $tpIds])->createCommand()->getRawSql());

            if ($partsLink !== null)
                $counterPart1 += count(TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $event->id])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $tpIds])->all()) + $counterTeam;
            else
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;

        }

        return $counterPart1;
    }

    //получить всех призеров и победителей мероприятий заданного уровня
    /*
    * event_level - уровень мероприятия
    * events_id - список id мероприятий, соответствующих внешнему доп. условию (группы) [0 - без условия]
    * $events_id2 - список id учеников, соответствующих внешнему доп. условию (группы) [0 - без условия]
    * $start_date - левая дата для поиска групп
    * $end_date - правая дата для поиска групп
    * $branch_id - id отдела, производящего учет (0 - все отделы)
    */
    static public function GetPrizesWinners($event_level, $events_id, $events_id2, $start_date, $end_date, $branch_id, $focus_id)
    {
        if ($events_id == 0)
            $events1 = ForeignEventWork::find()->where(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->all();
        else
            $events1 = ForeignEventWork::find()->where(['IN', 'id', $events_id])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->all();


        $partsLink = null;
        $pIds = [];
        if ($branch_id !== 0)
        {
            $eIds = [];
            foreach ($events1 as $event) $eIds[] = $event->id;


            if ($focus_id !== 0)
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->andWhere(['teacherParticipant.focus' => $focus_id])->all();
            else
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->all();

            foreach ($partsLink as $part) $pIds[] = $part->teacherParticipant->participant_id;

        }


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
                    if ($partsLink !== null)
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $pIds])->one();
                    else
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 1])->one();
                    if ($res !== null) $counterTeamWinners++;
                    else $counterTeamPrizes++;
                    
                    if ($partsLink !== null)
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['IN', 'participant_id', $pIds])->one();
                    else
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                }
                $tIds[] = $team;
            }

            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            if ($partsLink !== null)
            {
                if ($events_id2 == 0)
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $pIds])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $pIds])->all();
                }
                else
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $events_id2])->andWhere(['IN', 'participant_id', $pIds])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $events_id2])->andWhere(['IN', 'participant_id', $pIds])->all();
                }
                
            }
            else
            {
                if ($events_id2 == 0)
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->all();
                }
                else
                {
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $events_id2])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'participant_id', $events_id2])->all();
                }
                
            }
            


            $counter1 += count($achieves1) + $counterTeamPrizes;
            $counter2 += count($achieves2) + $counterTeamWinners;
            $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;
            $allTeams += $counterTeam;

        }

        return [$counter1, $counter2];
    }


    static public function DownloadEffectiveContract($start_date, $end_date, $budget)
    {
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_EC.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_EC.xlsx');
        //var_dump($inputData);

        $tgIds = [];


        $trainingGroups1 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['IN', 'budget', $budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['IN', 'budget', $budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['IN', 'budget', $budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['IN', 'budget', $budget])])
            ->all();


        
        foreach ($trainingGroups1 as $trainingGroup) $tgIds[] = $trainingGroup->id;
        //Получаем количество учеников
        /*
        $trainingGroups1 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['IN', 'budget', $budget])
            ->all();

        
        foreach ($trainingGroups1 as $trainingGroup) $tgIds[] = $trainingGroup->id;

        $trainingGroups2 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])
            ->andWhere(['IN', 'budget', $budget])
            ->all();

        foreach ($trainingGroups2 as $trainingGroup) $tgIds[] = $trainingGroup->id;

        $trainingGroups3 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])
            ->andWhere(['IN', 'budget', $budget])
            ->all();

        foreach ($trainingGroups3 as $trainingGroup) $tgIds[] = $trainingGroup->id;

        $trainingGroups4 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])
            ->andWhere(['IN', 'budget', $budget])
            ->all();

        foreach ($trainingGroups4 as $trainingGroup) $tgIds[] = $trainingGroup->id;
        */

        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->all();

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 4, 'на "'.substr($end_date, -2).'".'.substr($end_date, 5, 2).'.'.substr($end_date, 0, 4).' г.');
        $inputData->getActiveSheet()->getCellByColumnAndRow(3, 4)->getStyle()->getFont()->setBold();
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

        $result = ExcelWizard::GetPrizesWinners(8, 0, 0, $start_date, $end_date, 0, 0);
        
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 6, $result[0]);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 7, $result[1]);

        //----------------------------------

        //Всероссийские победители и призеры

        $result = ExcelWizard::GetPrizesWinners(7, 0, 0, $start_date, $end_date, 0, 0);
        
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 8, $result[0]);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 9, $result[1]);

        //----------------------------------

        //Региональные победители и призеры

        $result = ExcelWizard::GetPrizesWinners(6, 0, 0, $start_date, $end_date, 0, 0);

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 10, $result[0]);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 11, $result[1]);

        //----------------------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    static public function DownloadDoDop1($start_date, $end_date, $budget)
    {
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_DOP.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_DOP.xlsx');
        //var_dump($inputData);

        //Получаем количество учеников по техническим программам
        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->all();
        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->all();
        $participants2 = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['participant.sex' => 'Женский'])->all();

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 6, count($participants));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 6, count($participants2));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();
        //$newParticipants = $participants;

        //var_dump($newParticipants);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 6, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 6, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 6, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 6, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 6, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(8, 6, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 6, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 6, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(11, 6, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(12, 6, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 6, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(14, 6, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 6, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 6, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 6, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 6, count($participants));

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 6, count($participants));

        //----------------------------------

        //Получаем количество учеников по художественным программам
        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->all();
        $groupsId = [];
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->all();
        $participants2 = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['participant.sex' => 'Женский'])->all();


        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 10, count($participants));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 10, count($participants2));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();
        //$newParticipants = $participants;

        //var_dump($newParticipants);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 10, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 10, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 10, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 10, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 10, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(8, 10, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 10, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 10, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(11, 10, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(12, 10, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 10, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(14, 10, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 10, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 10, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 10, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 2, 2).'-01-01'));


        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 10, count($participants));

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 10, count($participants));

        //----------------------------------

        //Получаем количество учеников по социально-педагогическим программам
        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->all();
        $groupsId = [];
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->all();
        $participants2 = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['participant.sex' => 'Женский'])->all();


        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 9, count($participants));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 9, count($participants2));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();
        //$newParticipants = $participants;

        //var_dump($newParticipants);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 9, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 9, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 9, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 9, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 9, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(8, 9, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 9, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 9, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(11, 9, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(12, 9, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 9, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(14, 9, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 9, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 9, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 9, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 2, 2).'-01-01'));

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 9, count($participants));

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 9, count($participants));

        //----------------------------------

        //Получаем количество учеников по естественнонаучным программам
        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->all();
        $groupsId = [];
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->all();
        $participants2 = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['participant.sex' => 'Женский'])->all();


        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 7, count($participants));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 7, count($participants2));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();
        //$newParticipants = $participants;

        //var_dump($newParticipants);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 7, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 7, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 7, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 7, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 7, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(8, 7, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 7, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 7, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(11, 7, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(12, 7, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 7, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(14, 7, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 7, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 7, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 2, 2).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 7, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 2, 2).'-01-01'));

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 7, count($participants));

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 7, count($participants));

        //----------------------------------

        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 3, substr($start_date, 2, 2));


        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    static private function GetParticipantsByAge($age, $participants, $date)
    {
        $participantsId = [];
        foreach ($participants as $participant){
            if (round(floor((strtotime($date) - strtotime($participant->birthdate))) / (60 * 60 * 24 * 365.25)) == $age)
                $participantsId[] = $participant->id;
        }
        return count($participantsId);
    }

    static public function GetGroupsByBranchAndFocus($branch_id, $focus_id)
    {
        $programs = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'trainingProgram.focus_id', $focus_id])->all();
        if ($focus_id == 0)
        {
            $programs = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->all();
        }
        $tpIds = [];
        foreach ($programs as $program) $tpIds[] = $program->training_program_id;

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'trainingProgram.id', $tpIds])->andWhere(['branch_id' => $branch_id])->andWhere(['budget' => 1])->all();

        
        $gIds = [];
        foreach ($groups as $group) $gIds[] = $group->id;


        return $gIds;
    }

    static public function GetGroupsByDatesBranchFocus($start_date, $end_date, $branch_id, $focus_id)
    {
        $groups = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();


        $gIds = [];
        foreach ($groups as $group) $gIds[] = $group->training_group_id;

        return $gIds;
    }

    //получаем процент победителей и призеров от общего числа участников
    static public function GetPercentEventParticipants($start_date, $end_date, $branch_id, $focus_id, $budget)
    {
        $winners1 = ExcelWizard::GetPrizesWinners(8, 0, 0, $start_date, $end_date, $branch_id, $focus_id);
        $winners2 = ExcelWizard::GetPrizesWinners(7, 0, 0, $start_date, $end_date, $branch_id, $focus_id);
        $winners3 = ExcelWizard::GetPrizesWinners(6, 0, 0, $start_date, $end_date, $branch_id, $focus_id);
        $all = ExcelWizard::GetAllParticipantsForeignEvents(8, 0, 0, $start_date, $end_date, $branch_id, $focus_id) + ExcelWizard::GetAllParticipantsForeignEvents(7, 0, 0, $start_date, $end_date, $branch_id, $focus_id) + ExcelWizard::GetAllParticipantsForeignEvents(6, 0, 0, $start_date, $end_date, $branch_id, $focus_id);
        
        if ($all == 0) return 0;
        return round((($winners1[0] + $winners1[1] + $winners2[0] + $winners2[1] + $winners3[0] + $winners3[1]) / $all) * 100);
    }

    //получаем данные по людям, которые обучались в 2+ группах
    static public function GetPercentDoubleParticipant($start_date, $end_date, $branch_id, $focus_id)
    {
        $unicParts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->select('participant_id')->distinct()->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();

        $allParts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->select('participant_id')->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();

        if (count($unicParts) == 0) return 0;
        return round((count($allParts) - count($unicParts)) / count($unicParts) * 100);
    }

    //получаем данные по проектам людей (получившие сертификат)
    static public function GetPercentProjectParticipant($start_date, $end_date, $branch_id, $focus_id)
    {
        $projectParts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->andWhere(['not', ['certificat_number' => null]])
            ->all();

        $allParts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->select('participant_id')->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();

        if (count($projectParts) == 0) return 0;
        return round((count($projectParts) / count($allParts)) * 100);
    }

    static public function DownloadGZ($start_date, $end_date, $visit_flag)
    {
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_GZ.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_GZ.xlsx');

        //получаем количество детей, подавших более 1 заявления и считаем процент защитивших проект / призеров победителей мероприятий

        //Отдел Технопарк (тех. направленность)

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 2, 1));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 18, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 2, 1));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 19, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 2, 1, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 16)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 16)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 18)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 18)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 19)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 19)->getStyle()->getAlignment()->setHorizontal('center');

        //-------------------------------------

        //Отдел ЦДНТТ (тех. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 21, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 3, 1));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 23, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 3, 1, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 21)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 21)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 23)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 23)->getStyle()->getAlignment()->setHorizontal('center');

        //---------------------------------

        //Отдел ЦДНТТ (худ. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 25, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 3, 2));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 27, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 3, 2, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 25)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 25)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 27)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 27)->getStyle()->getAlignment()->setHorizontal('center');

        //---------------------------------

        //Отдел ЦДНТТ (соц-пед. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 29, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 3, 3));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 31, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 3, 3, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 29)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 29)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 31)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 31)->getStyle()->getAlignment()->setHorizontal('center');

        //-------------------------------------

        //Отдел Кванториум (тех. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 33, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 1, 1));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 35, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 1, 1));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 36, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 1, 1, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 33)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 33)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 35)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 35)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 36)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 36)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------

        //Отдел Моб. Кванториум (тех. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 39, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 4, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 39)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 39)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------

        //Отдел ЦОД (естес.-науч. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 47, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 7, 4));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 49, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 7, 4));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 50, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 7, 4, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 47)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 47)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 49)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 49)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 50)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 50)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------

        //Отдел ЦОД (худож. направленность)
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 52, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 7, 4));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 54, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 7, 4));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 55, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 7, 4, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 52)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 52)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 54)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 54)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 55)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 55)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------

        //-----------------------------------------------------

        //Кол-во человеко-часов

        $statusArr = [];
        if ($visit_flag == 1) $statusArr = [0, 1, 2];
        else $statusArr = [0, 2];


        //Отдел Технопарк (тех. направленность)


        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 2, 1)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();


        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 8, count($visits));

        //---------------

        //Отдел ЦДНТТ (тех. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 1)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 9, count($visits));

        //---------------

        //Отдел ЦДНТТ (худ. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 2)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 10, count($visits));

        //---------------

        //Отдел ЦДНТТ (соц-пед. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 3)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 11, count($visits));

        //---------------

        //Отдел Кванториум (тех. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 1, 0)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 12, count($visits));

        //---------------

        //Отдел Моб. Кванториум (тех. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 4, 1)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 13, count($visits));

        //---------------

        //Отдел ЦОД (естес.-науч. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 4)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 16, count($visits));

        //---------------

        //Отдел ЦОД (худож. направленность)

        $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 2)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 17, count($visits));

        //---------------

        //---------------------
        

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    /*
    static private function GetParticipantsByAge($age, $participants, $date)
    {
        $participantsId = [];
        foreach ($participants as $participant){
            if (round(floor((strtotime($date) - strtotime($participant->participant->birthdate))) / (60 * 60 * 24 * 365.25)) == $age)
                $participantsId[] = $participant->participant_id;
        }
        return count($participantsId);
    }
    */

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

    static public function DownloadTeacherDocumentOrder($order_id)
    {
        ini_set('memory_limit', '512M');

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/test.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/test.xlsx');

        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $c = 1;

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 12 + $c, $c);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=test.xls");
        header("Content-Transfer-Encoding: binary ");
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel5');
        $writer->save('php://output');
        exit;
    }
}