<?php

//--g
namespace app\models\components;


use app\models\common\ForeignEventParticipants;
use app\models\common\RussianNames;
use app\models\extended\JournalModel;
use app\models\work\CompanyWork;
use app\models\work\DocumentOrderWork;
use app\models\work\ForeignEventWork;
use app\models\work\LessonThemeWork;
use app\models\work\OrderGroupParticipantWork;
use app\models\work\OrderGroupWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\ResponsibleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeamWork;
use app\models\work\BranchWork;
use app\models\work\FocusWork;
use app\models\work\ThematicPlanWork;
use app\models\work\BranchProgramWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\GroupProjectThemesWork;
use app\models\work\PeopleWork;
use app\models\work\VisitWork;
use app\models\work\AuditoriumWork;
use app\models\work\CertificatWork;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use \PHPExcel_Style_Border;

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
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/training-program/temp/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/training-program/temp/'.$filename);
        $index = 2;
        while ($index <= $inputData->getActiveSheet()->getHighestRow() && strlen($inputData->getActiveSheet()->getCellByColumnAndRow(0, $index)->getValue()) > 1)
        {
            $theme = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $index)->getValue();
            $controlId = $inputData->getActiveSheet()->getCellByColumnAndRow(1, $index)->getValue();
            $tp = new ThematicPlanWork();
            if (gettype($theme) == "object")
                $theme = $theme->getPlainText();
            $theme = preg_replace('/\s+/', ' ', $theme);
            $tp->theme = $theme;
            $tp->control_type_id = $controlId;
            $tp->training_program_id = $training_program_id;
            $tp->save();
            $index++;
        }
        unlink(Yii::$app->basePath.'/upload/files/training-program/temp/'.$filename);
    }

    static public function WriteContractors($filename)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/'.$filename);

        $index = 1;

        while ($index <= $inputData->getActiveSheet()->getHighestRow())
        {
            $inn = strval($inputData->getActiveSheet()->getCellByColumnAndRow(1, $index)->getValue());
            $name = $inputData->getActiveSheet()->getCellByColumnAndRow(2, $index)->getValue();
            $short_name = $inputData->getActiveSheet()->getCellByColumnAndRow(3, $index)->getValue();
            $smsp = $inputData->getActiveSheet()->getCellByColumnAndRow(4, $index)->getValue();
            $ownership = $inputData->getActiveSheet()->getCellByColumnAndRow(5, $index)->getValue();
            $phone_number = $inputData->getActiveSheet()->getCellByColumnAndRow(12, $index)->getValue();
            $okved = $inputData->getActiveSheet()->getCellByColumnAndRow(9, $index)->getValue();
            $email = $inputData->getActiveSheet()->getCellByColumnAndRow(13, $index)->getValue();
            $head_fio = $inputData->getActiveSheet()->getCellByColumnAndRow(10, $index)->getValue();

            $smsp_r = function ($value) {
                if ($value == 'Микропредприятие') return 1;
                if ($value == 'Малое предприятие') return 2;
                if ($value == 'Среднее предприятие') return 3;
                if ($value == 'НЕ СМСП') return 7;
                return null;
            };

            $owner_r = function ($value) {
                if ($value == 'Бюджетное') return 1;
                if ($value == 'Автономное') return 2;
                if ($value == 'Казённое') return 3;
                if ($value == 'Унитарное') return 4;
                if ($value == 'НКО') return 5;
                if ($value == 'Нетиповое') return 6;
                if ($value == 'ООО') return 7;
                if ($value == 'ИП') return 8;
                if ($value == 'ПАО') return 9;
                if ($value == 'АО') return 10;
                if ($value == 'ЗАО') return 11;
                if ($value == 'Физлицо') return 12;
                return 13;
            };

            $newCompany = CompanyWork::find()->where(['inn' => $inn])->one();
            if ($newCompany === null) $newCompany = new CompanyWork();
            $newCompany->inn = $inn;
            $newCompany->name = $name;
            $newCompany->short_name = $short_name;
            $newCompany->category_smsp_id = $smsp_r($smsp);
            $newCompany->ownership_type_id = $owner_r($ownership);
            $newCompany->phone_number = $phone_number;
            $newCompany->okved = $okved;
            $newCompany->email = $email;
            $newCompany->head_fio = $head_fio;
            $newCompany->is_contractor = 1;
            $newCompany->save();

            if (count($newCompany->getErrors()) > 0)
            {
                var_dump($newCompany->getErrors());
                var_dump('<br>');
                var_dump($newCompany);
                var_dump('<br><br>');
            }

            $index++;
        }
    }

    static public function DownloadKUG($training_group_id)
    {
        ini_set('memory_limit', '512M');

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/template_KUG.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/template_KUG.xlsx');


        $lessons = LessonThemeWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $training_group_id])
                                        ->orderBy(['trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.lesson_start_time' => SORT_ASC])->all();
        $c = 1;

        $styleArray = array('fill'    => array(
            'type'      => 'solid',
            'color'     => array('rgb' => 'FFFFFF')
        ),
            'borders' => array(
                'bottom'    => array('style' => 'thin'),
                'right'     => array('style' => 'thin'),
                'top'     => array('style' => 'thin'),
                'left'     => array('style' => 'thin')
            )
        );

        foreach ($lessons as $lesson)
        {
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 11 + $c, $c);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, 11 + $c, $lesson->trainingGroupLesson->lesson_date);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 11 + $c, mb_substr($lesson->trainingGroupLesson->lesson_start_time, 0, -3).' - '.mb_substr($lesson->trainingGroupLesson->lesson_end_time, 0, -3));

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 11 + $c, $lesson->theme);
            $inputData->getActiveSheet()->getRowDimension(11 + $c)->setRowHeight(12.5 * (strlen($lesson->theme) / 60) + 15);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(4, 11 + $c, $lesson->trainingGroupLesson->duration);
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(5, 11 + $c, "Групповая");
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(6, 11 + $c, $lesson->controlType->name);
            $c++;
        }

        for ($i = 11; $i < 11 + count($lessons); $i++)
        {
            $inputData->getSheet(0)->getStyle('A'.$i.':B'.($i+1))->applyFromArray($styleArray);
            $inputData->getSheet(0)->getStyle('B'.$i.':C'.($i+1))->applyFromArray($styleArray);
            $inputData->getSheet(0)->getStyle('C'.$i.':D'.($i+1))->applyFromArray($styleArray);
            $inputData->getSheet(0)->getStyle('D'.$i.':E'.($i+1))->applyFromArray($styleArray);
            $inputData->getSheet(0)->getStyle('E'.$i.':F'.($i+1))->applyFromArray($styleArray);
            $inputData->getSheet(0)->getStyle('F'.$i.':G'.($i+1))->applyFromArray($styleArray);
        }
        $inputData->getSheet(0)->getStyle('A12:G'. (11 + count($lessons)))->applyFromArray($styleArray);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=kug.xls");
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


    static public function GetAllParticipantsFromBranch($start_date, $end_date, $branch_id, $focus_id, $unic)
    {
        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->all();

        $tgIds = [];
        foreach ($trainingGroups as $group) $tgIds[] = $group->id;


        if ($unic == 0)
            $parts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->where(['IN', 'trainingGroup.id', $tgIds]);
        else
            $parts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->select('participant_id')->distinct()->where(['IN', 'trainingGroup.id', $tgIds]);

        if (count($branch_id) > 0) $parts = $parts->andWhere(['IN', 'trainingGroup.branch_id', $branch_id]);
        if (count($focus_id) > 0) $parts = $parts->andWhere(['IN', 'trainingProgram.focus_id', $focus_id]);

        return count($parts->all());
    }

    //получить всех участников заданного отдела мероприятий в заданный период
    static public function GetAllParticipantsForeignEvents
    ($event_level, $events_id, $events_id2, $start_date, $end_date, $branch_id, $focus_id, $allow_remote = 1)
    {
        if ($events_id == 0)
            $events1 = ForeignEventWork::find()->where(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['event_level_id' => $event_level])->all();
        else
            $events1 = ForeignEventWork::find()->where(['IN', 'id', $events_id])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['IN', 'event_level_id', $event_level])->all();


        $partsLink = null;
        $pIds = [];
        $eIds = [];
        if ($branch_id !== 0)
        {
            
            foreach ($events1 as $event) $eIds[] = $event->id;

            if ($focus_id !== 0)
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                    ->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])
                    ->andWhere(['teacher_participant_branch.branch_id' => $branch_id])
                    ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                    ->andWhere(['teacherParticipant.focus' => $focus_id])->all();
            else
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                    ->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])
                    ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                    ->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->all();

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
                    if (count($partsLink) !== 0)
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])
                            ->andWhere(['foreign_event_id' => $team->foreign_event_id])
                            ->andWhere(['allow_remote_id' => $allow_remote])
                            ->andWhere(['IN', 'participant_id', $pIds])->one();
                    else
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])
                            ->andWhere(['allow_remote_id' => $allow_remote])
                            ->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
                    if ($res !== null) $counterTeam++;
                }
                $tIds[] = $team;
            }

            $tpIds = [];
            foreach ($tIds as $tId)
                $tpIds[] = $tId->participant_id;

            //var_dump(TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $event->id])->andWhere(['teacher_participant_branch.branch_id' => $branch_id])->andWhere(['NOT IN', 'teacherParticipant.participant_id', $tpIds])->createCommand()->getRawSql());
            //var_dump($counterTeam);
            if (count($partsLink) !== 0)
                $counterPart1 += count(TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                        ->where(['teacherParticipant.foreign_event_id' => $event->id])
                        ->andWhere(['teacher_participant_branch.branch_id' => $branch_id])
                        ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                        ->andWhere(['NOT IN', 'teacherParticipant.participant_id', $tpIds])->all()) + $counterTeam;
            else
                $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])
                        ->andWhere(['allow_remote_id' => $allow_remote])
                        ->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;

        }

        //var_dump($counterPart1);

        return $counterPart1;
    }

    //получить пофамильно победителей и призеров всех мероприятий
    /*
    * event_level - уровни мероприятий
    * branch_id - отделы, производящий учет
    * $start_date - левая дата для поиска мероприятий
    * $end_date - правая дата для поиска мероприятий
    */
    static public function GetParticipantAchievements($event_level, $branch_id, $start_date, $end_date)
    {
        $teacherPart = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->joinWith(['teacherParticipant.foreignEvent foreignEvent'])->where(['IN', 'foreignEvent.event_level_id', $event_level])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['>=', 'foreignEvent.finish_date', $start_date])->andWhere(['<=', 'foreignEvent.finish_date', $end_date])->all();

        $parts = [];
        $partsOrig = [];
        foreach ($teacherPart as $one)
        {
            $temp = ParticipantAchievementWork::find()->where(['foreign_event_id' => $one->teacherParticipant->foreign_event_id])->andWhere(['participant_id' => $one->teacherParticipant->participant_id])->one();
            if ($temp !== null)
            {
                $partsOrig[] = TeacherParticipantWork::find()->where(['id' => $one->teacher_participant_id])->one();
                $parts[] = $temp;
            }
        }

        return [$parts, $partsOrig];
    }

    static private function IsDublicateTeam($events, $teamNames, $check_event, $check_teamName)
    {
        for ($i = 0; $i < count($events); $i++)
            if ($events[$i] == $check_event && $teamNames[$i] == $check_teamName)
                return true;

        return false;
    }

    //ПРЕОБРАЗОВАТЬ В УНИВЕРСАЛЬНУЮ ФУНКЦИЮ!!!
    static private function DoubleCheckArray($arr1, $arr2, $check1, $check2)
    {
        for ($i = 0; $i < count($arr1); $i++)
            if ($arr1[$i] == $check1 && $arr2[$i] == $check2)
                return true;

        return false;
    }

    //ПРЕОБРАЗОВАТЬ В УНИВЕРСАЛЬНУЮ ФУНКЦИЮ!!!
    static private function TripleCheckArray($arr1, $arr2, $arr3, $check1, $check2, $check3)
    {
        for ($i = 0; $i < count($arr1); $i++)
            if ($arr1[$i] == $check1 && $arr2[$i] == $check2 && $arr3[$i] == $check3)
                return true;

        return false;
    }

    static public function GetDetailPrizeWinners($event_level, $branch_id, $start_date, $end_date)
    {
        $teacherPart = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->joinWith(['teacherParticipant.foreignEvent foreignEvent'])->where(['IN', 'foreignEvent.event_level_id', $event_level])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['>=', 'foreignEvent.finish_date', $start_date])->andWhere(['<=', 'foreignEvent.finish_date', $end_date])->all();

        $result = [];
        foreach ($teacherPart as $one)
        {
            $temp = ParticipantAchievementWork::find()->where(['foreign_event_id' => $one->teacherParticipant->foreign_event_id])->andWhere(['participant_id' => $one->teacherParticipant->participant_id])->one();

            if ($temp !== null) $result[] = $temp->id;
        }

        $result = ParticipantAchievementWork::find()->where(['IN', 'id', $result])->all();
        
        return $result;
    }

    static public function NewGetPrizeWinners($event_level, $branch_id, $start_date, $end_date)
    {
        $teacherPart = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->joinWith(['teacherParticipant.foreignEvent foreignEvent'])->where(['IN', 'foreignEvent.event_level_id', $event_level])->andWhere(['IN', 'teacher_participant_branch.branch_id', $branch_id])->andWhere(['>=', 'foreignEvent.finish_date', $start_date])->andWhere(['<=', 'foreignEvent.finish_date', $end_date])->all();

        //выборка команд

        $notIncludeIds = [];
        $teamPartWinIds = [];
        $teamPartPrizeIds = [];
        $prizeTeam = 0;
        $winTeam = 0;

        $eventsId = [];
        $teamNames = [];

        foreach ($teacherPart as $one)
        {

            $teamPart = TeamWork::find()->where(['foreign_event_id' => $one->teacherParticipant->foreign_event_id])->andWhere(['participant_id' => $one->teacherParticipant->participant_id])->andWhere(['NOT IN', 'id', $notIncludeIds])->one();

            $allTeamParts = TeamWork::find()->where(['name' => $teamPart->name])->andWhere(['foreign_event_id' => $teamPart->foreign_event_id])->all();

            if (!ExcelWizard::IsDublicateTeam($eventsId, $teamNames, $teamPart->foreign_event_id, $teamPart->name))
            {
                $eventsId[] = $teamPart->foreign_event_id;
                $teamNames[] = $teamPart->name;

                //var_dump($teamPart->name.' '.count($allTeamParts).'<br>');

                if (count($allTeamParts) > 0)
                {
                    $temp = ParticipantAchievementWork::find()->where(['foreign_event_id' => $allTeamParts[0]->foreign_event_id])->andWhere(['participant_id' => $allTeamParts[0]->participant_id])->one();

                    if ($temp->winner == 0) $prizeTeam += 1;
                    else $winTeam += 1;
                }

            }
            
            if (count($allTeamParts) > 0)
            {

                foreach ($allTeamParts as $onePart)
                {
                    $temp = ParticipantAchievementWork::find()->where(['foreign_event_id' => $onePart->foreign_event_id])->andWhere(['participant_id' => $onePart->participant_id])->one();

                    if ($temp->winner == 0) $teamPartPrizeIds[] = $temp->id;
                    else $teamPartWinIds[] = $temp->id;

                    $notIncludeIds[] = $onePart->id;
                }
            }

        }

        //--------------


        $prize = [];
        $winners = [];

        $evLevel = [];
        $partId = [];
        $achieves = [];
        foreach ($teacherPart as $one)
        {
            $temp = ParticipantAchievementWork::find()->where(['foreign_event_id' => $one->teacherParticipant->foreign_event_id])->andWhere(['participant_id' => $one->teacherParticipant->participant_id])->one();
            $isTeam = TeamWork::find()->where(['foreign_event_id' => $temp->foreign_event_id])->andWhere(['participant_id' => $temp->participant_id])->one();

            if ($temp !== null && $isTeam == null && !ExcelWizard::TripleCheckArray($evLevel, $partId ,$achieves, $temp->foreignEvent->event_level_id, $temp->participant_id, $temp->winner))
            {
                if ($temp->winner == 0) $prize[] = $temp;
                else $winners[] = $temp;
            }

            if (!ExcelWizard::TripleCheckArray($evLevel, $partId ,$achieves, $temp->foreignEvent->event_level_id, $temp->participant_id, $temp->winner))
            {
                $evLevel[] = $temp->foreignEvent->event_level_id;
                $partId[] = $temp->participant_id;
                $achieves[] = $temp->winner;
            }
        }

        return [count($winners)/* - count($teamPartWinIds)*/, count($prize)/* - count($teamPartPrizeIds)*/, $winTeam, $prizeTeam];
    }

    //получить всех призеров и победителей мероприятий заданного уровня
    /*
    * event_level - уровень мероприятия
    * events_id - список id мероприятий, соответствующих внешнему доп. условию (группы) [0 - без условия]
    * $events_id2 - список id учеников, соответствующих внешнему доп. условию (группы) [0 - без условия]
    * $start_date - левая дата для поиска групп
    * $end_date - правая дата для поиска групп
    * $branch_id - id отдела, производящего учет (0 - все отделы)
    * $allow_remote - форма реализации
    */
    static public function GetPrizesWinners($event_level, $events_id, $events_id2, $start_date, $end_date, $branch_id, $focus_id, $participants_not_include, $allow_remote = 1)
    {
        $not_include = $participants_not_include;

        if ($events_id == 0)
            $events1 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])
                ->where(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])
                ->andWhere(['event_level_id' => $event_level])/*->andWhere(['teacherParticipantBranches.branch_id' => $branch_id])*/->all();
        else
            $events1 = ForeignEventWork::find()->joinWith(['teacherParticipants teacherParticipants'])->joinWith(['teacherParticipants.teacherParticipantBranches teacherParticipantBranches'])
                ->where(['IN', 'id', $events_id])->andWhere(['>=', 'finish_date', $start_date])
                ->andWhere(['<=', 'finish_date', $end_date])
                ->andWhere(['event_level_id' => $event_level])/*->andWhere(['teacherParticipantBranches.branch_id' => $branch_id])*/->all();


        
        $partsLink = null;
        $pIds = [];
        $eIds = [];
        foreach ($events1 as $event) $eIds[] = $event->id;
        if ($branch_id !== 0)
        {
            
            
            if ($focus_id !== 0)
            {
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                    ->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])
                    ->andWhere(['teacher_participant_branch.branch_id' => $branch_id])
                    ->andWhere(['teacherParticipant.focus' => $focus_id])
                    ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                    ->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();
            }
            else
                $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                    ->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])
                    ->andWhere(['teacher_participant_branch.branch_id' => $branch_id])
                    ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                    ->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();
            

        }
        else
        {
            $partsLink = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                ->where(['IN', 'teacherParticipant.foreign_event_id', $eIds])
                ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                ->andWhere(['NOT IN', 'teacherParticipant.participant_id', $participants_not_include])->all();
        }

        /*if ($branch_id == 7 && $focus_id == 2)
        {
            foreach ($partsLink as $part)
                echo $part->teacherParticipant->foreign_event_id.' '.$part->teacherParticipantWork->participantWork->fullName.'<br>';
        }*/

        $eIds = [];
        foreach ($partsLink as $part) 
        {
            $pIds[] = $part->teacherParticipant->participant_id;
            $eIds[] = $part->teacherParticipant->foreign_event_id;
        }
        /*var_dump($eIds);
        var_dump($partsLink);*/
        $events1 = ForeignEventWork::find()->where(['IN', 'id', $eIds])->all();




        foreach ($pIds as $one) $not_include[] = $one;


        $counter1 = 0;
        $counter2 = 0;
        $counterPart1 = 0;
        $allTeams = 0;
        foreach ($events1 as $event)
        {

            $participantsEvent = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                ->where(['teacherParticipant.foreign_event_id' => $event->id])
                ->andWhere(['teacher_participant_branch.branch_id' => $branch_id])
                ->andWhere(['teacherParticipant.allow_remote_id' => $allow_remote])
                ->andWhere(['teacherParticipant.focus' => $focus_id])->all();


            $pIds = [];
            foreach ($participantsEvent as $part) $pIds[] = $part->teacherParticipant->participant_id;

            $teams = TeamWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $pIds])->orderBy(['name' => SORT_ASC])->all();
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

                    if ($partsLink !== null)
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 0])->andWhere(['IN', 'participant_id', $pIds])->one();
                    else
                        $res = ParticipantAchievementWork::find()->where(['participant_id' => $team->participant_id])->andWhere(['foreign_event_id' => $team->foreign_event_id])->andWhere(['winner' => 0])->one();
                    if ($res !== null) $counterTeamPrizes++;
                    
                    if ($partsLink !== null)
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])
                            ->andWhere(['foreign_event_id' => $team->foreign_event_id])
                            ->andWhere(['allow_remote_id' => $allow_remote])
                            ->andWhere(['IN', 'participant_id', $pIds])->one();
                    else
                        $res = TeacherParticipantWork::find()->where(['participant_id' => $team->participant_id])
                            ->andWhere(['allow_remote_id' => $allow_remote])
                            ->andWhere(['foreign_event_id' => $team->foreign_event_id])->one();
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
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'foreign_event_id', $events_id2])->andWhere(['IN', 'participant_id', $pIds])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'foreign_event_id', $events_id2])->andWhere(['IN', 'participant_id', $pIds])->all();
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
                    $achieves1 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 0])->andWhere(['IN', 'foreign_event_id', $events_id2])->all();
                    $achieves2 = ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['NOT IN', 'participant_id', $tpIds])->andWhere(['winner' => 1])->andWhere(['IN', 'foreign_event_id', $events_id2])->all();
                }
                
            }


            
            $achievesId1 = [];
            foreach ($achieves1 as $achieve) $achievesId1[] = $achieve->participant_id;
            foreach ($achieves2 as $achieve) $achievesId1[] = $achieve->participant_id;
            $achievesId1 = array_unique($achievesId1);

            /*if ($branch_id == 3 && $focus_id == 3)
            {
                if (count($achievesId1) > 0)
                    echo $event->name.' '.$event->id.'<br>';
                for ($i = 0; $i < count($achievesId1); $i++)
                {
                    $part = ForeignEventParticipantsWork::find()->where(['id' => $achievesId1[$i]])->one();
                    echo $part->fullName.'<br>';
                }
                echo '<br> Все:<br>';
                $alls = TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['IN', 'participant_id', $pIds])->andWhere(['NOT IN', 'participant_id', $tpIds])->all();

                foreach ($alls as $one)
                    echo $one->participantWork->fullName.'<br>';

                echo '<br>---<br>';
            }*/

            $counter1 += count($achieves1) + $counterTeamPrizes;
            $counter2 += count($achieves2) + $counterTeamWinners;
            $counterGZ += count($achievesId1);
            $counterPart1 += count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['allow_remote_id' => $allow_remote])->andWhere(['IN', 'participant_id', $pIds])->andWhere(['NOT IN', 'participant_id', $tpIds])->all()) + $counterTeam;
            $allTeams += $counterTeam;

            if ($branch_id == 3 && $focus_id == 3)
            {
                if ($counterPart1 < $counter1 + $counter2)
                {
                    echo $event->name.' '.$counterPart1.' '.$counter1.' '.$counter2.'<br>---<br>';
                    $allP = TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])
                        ->andWhere(['allow_remote_id' => $allow_remote])
                        ->andWhere(['IN', 'participant_id', $pIds])
                        ->andWhere(['NOT IN', 'participant_id', $tpIds])->all();

                    foreach ($allP as $oneP) echo $oneP->participantWork->fullName.'<br>';
                    echo '<<<>>><br>';
                    foreach ($achieves1 as $oneP) echo $oneP->participantWork->fullName.'<br>';
                    foreach ($achieves2 as $oneP) echo $oneP->participantWork->fullName.'<br>';

                    echo '-------------------------<br><br>';
                }
            }


        }


        return [$counter1, $counter2, $not_include, $counterGZ, $counterPart1, $allTeams];
    }

    //получаем всех учеников, успешно завершивших и/или проходящих обучение в период со $start_date по $end_date из групп $group_ids
    static public function GetParticipantsIdsFromGroups($group_ids)
    {
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $group_ids])->all(); //получаем всех учеников из групп

        $result = [];
        foreach ($participants as $participant) $result[] = $participant->participant_id;

        return $result;

    }

    //получаем всех учеников, успешно завершивших и/или проходящих обучение в пеирод со $start_date по $end_date из групп $group_ids
    static public function GetParticipantsIdsByStatus($group_ids)
    {
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $group_ids])->all(); //получаем всех учеников из групп

        $pIds = [];

        foreach ($participants as $participant)
        {
            $orders = OrderGroupWork::find()->joinWith(['documentOrder documentOrder'])->joinWith(['trainingGroup trainingGroup'])->where(['training_group_id' => $participant->training_group_id])->andWhere(['<', 'documentOrder.order_date', new \yii\db\Expression('`trainingGroup`.`finish_date`')])->all();
            foreach ($orders as $order)
            {
                $pasta = OrderGroupParticipantWork::find()->where(['order_group_id' => $order->id])->andWhere(['group_participant_id' => $participant->id])->andWhere(['status' => 1])->all();
                foreach ($pasta as $makarona) $pIds[] = $makarona->groupParticipant->participant_id;
            }

        }

        if (count($pIds) !== 0)
            $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $group_ids])->andWhere(['NOT IN', 'participant_id', $pIds])->all();
        else
            $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $group_ids])->all();

        $result = [];
        foreach ($participants as $participant) $result[] = $participant->participant_id;

        return $result;

        /*
        $ogp1 = OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->joinWith(['orderGroup.documentOrder order'])->joinWith(['orderGroup.trainingGroup group'])->where(['IN', 'group.id'])->andWhere(['status' => 1])->andWhere(['>=', 'order.order_date', 'group.finish_date'])->all(); //получить всех отчисленных по успешному завершению

        foreach ($ogp1 as $one) $pIds[] = $ogp1->groupParticipant->participant_id;

        $ogp2 = OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->joinWith(['orderGroup.documentOrder order'])->joinWith(['orderGroup.trainingGroup group'])->where(['IN', 'group.id'])->andWhere(['status' => 0])->andWhere(['>=', 'order.order_date', 'group.start_date'])->andWhere(['<=', 'order.order_date', 'group.finish_date'])->all(); //получить всех зачисленных и еще не отчисленных

        foreach ($ogp2 as $one) $pIds[] = $ogp2->groupParticipant->participant_id;

        $participants = $participants->andWhere(['IN', 'participant_id', $piIds])->all();

        $result[];

        foreach ($participants as $one) $result[] = $one->participant_id;

        return $result;
        */
    }

    static private function InTeam($event_id, $participant_id)
    {
        $team = TeamWork::find()->where(['foreign_event_id' => $event_id])->andWhere(['participant_id' => $participant_id])->one();
        return $team !== null;
    }


    static public function DownloadEffectiveContract($start_date, $end_date, $budget)
    {
        

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_EC.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_EC.xlsx');
        //var_dump($inputData);

        $tgIds = [];


        /*$trainingGroups1 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['IN', 'budget', $budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['IN', 'budget', $budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['IN', 'budget', $budget])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['IN', 'budget', $budget])])
            ->all();
*/
        $trainingGroups1 = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
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

        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($tgIds)])->all();

        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 4, 'на "'.substr($end_date, -2).'".'.substr($end_date, 5, 2).'.'.substr($end_date, 0, 4).' г.');
        $inputData->getSheet(1)->getCellByColumnAndRow(3, 4)->getStyle()->getFont()->setBold();
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 5, count($participants));
        //----------------------------

        //Получаем мероприятия с выбранными учениками

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;
        $eventParticipants = TeacherParticipantWork::find()->where(['IN', 'participant_id', $pIds])->all();

        $eIds = [];
        foreach ($eventParticipants as $eventParticipant) $eIds[] = $eventParticipant->foreign_event_id;

        $eIds2 = [];
        foreach ($eventParticipants as $eventParticipant) $eIds2[] = $eventParticipant->participant_id;

        $events = ForeignEventWork::find()->andWhere(['>=', 'finish_date', $start_date])->andWhere(['<=', 'finish_date', $end_date]);



        //var_dump(count(ExcelWizard::GetParticipantAchievements([6, 7, 8], [1, 2, 3, 4, 7], $start_date, $end_date)[0]));
    

        //-------------------------------------------

        //Международные победители и призеры

        $result = ExcelWizard::NewGetPrizeWinners(8, [1, 2, 3, 4, 7], $start_date, $end_date);
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 6, $result[1] + $result[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 7, $result[0] + $result[2]);

        $newResult = ExcelWizard::GetDetailPrizeWinners([6, 7, 8], [1, 2, 3, 4, 7], $start_date, $end_date);
        //var_dump(count($newResult));
        for ($i = 0; $i < count($newResult); $i++)
        {
            $inputData->getSheet(2)->setCellValueByColumnAndRow(1, 5 + $i, $newResult[$i]->participantWork->secondname);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(2, 5 + $i, $newResult[$i]->foreignEvent->eventLevel->name);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 5 + $i, $newResult[$i]->foreignEvent->name);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 5 + $i, $newResult[$i]->nomination);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 5 + $i, ExcelWizard::InTeam($newResult[$i]->foreign_event_id, $newResult[$i]->participant_id) ? 'Групповая' : 'Индивидуальная');
            $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 5 + $i, $newResult[$i]->winner ? 'Победитель' : 'Призер');
            $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 5 + $i, $newResult[$i]->achievment);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 5 + $i, $newResult[$i]->cert_number);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 5 + $i, $newResult[$i]->date);
        }
        

        //----------------------------------

        //Всероссийские победители и призеры

        $result = ExcelWizard::NewGetPrizeWinners(7, [1, 2, 3, 4, 7], $start_date, $end_date);
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 8, $result[1] + $result[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 9, $result[0] + $result[2]);

        //----------------------------------

        //Региональные победители и призеры

        $result = ExcelWizard::NewGetPrizeWinners(6, [1, 2, 3, 4, 7], $start_date, $end_date);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 10, $result[1] + $result[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 11, $result[0] + $result[2]);

        //----------------------------------


        //--Пофамильная разбивка--
        /*
        $allAchieves = ExcelWizard::GetParticipantAchievements([6, 7, 8], [1, 2, 3, 4, 7], $start_date, $end_date);
        $row = 5;
        for ($i = 0; $i < count($allAchieves[0]); $i++)
        {
            $inputData->getSheet(2)->setCellValueByColumnAndRow(1, $row, $allAchieves[0][$i]->participantWork->secondname);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(2, $row, $allAchieves[0][$i]->foreignEvent->eventLevel->name);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(3, $row, $allAchieves[0][$i]->foreignEvent->name);
            $team = TeamWork::find()->where(['foreign_event_id' => $allAchieves[1][$i]->foreign_event_id])->andWhere(['participant_id' => $allAchieves[1][$i]->participant_id])->one();
            $inputData->getSheet(2)->setCellValueByColumnAndRow(5, $row, $team === null ? 'Индивидуальная' : 'Групповая');
            $inputData->getSheet(2)->setCellValueByColumnAndRow(6, $row, $allAchieves[0][$i]->winner == 0 ? 'Призер' : 'Победитель');
            $inputData->getSheet(2)->setCellValueByColumnAndRow(7, $row, $allAchieves[0][$i]->achievment);
            $row++;
        }
        */

        //------------------------

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

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->all();

        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 6, count($participants));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 6, count($participants2));




        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();


        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 6, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 6, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 6, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 6, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 6, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(8, 6, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 6, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 6, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(11, 6, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(12, 6, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 6, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(14, 6, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 6, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 6, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 6, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 6, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 6, count($participants2) - count($participants));

        //----------------------------------

        //Получаем количество учеников по художественным программам
        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->all();
        $groupsId = [];
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();


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

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsByStatus($groupsId)])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 10, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsByStatus($groupsId)])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 10, count($participants2) - count($participants));

        //----------------------------------

        //Получаем количество учеников по социально-педагогическим программам
        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->all();
        $groupsId = [];
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();



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

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsByStatus($groupsId)])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 9, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        
        
        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsByStatus($groupsId)])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 9, count($participants2) - count($participants));

        //----------------------------------

        //Получаем количество учеников по естественнонаучным программам
        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->all();
        $groupsId = [];
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();


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
        
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsByStatus($groupsId)])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 7, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsByStatus($groupsId)])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 7, count($participants2) - count($participants));

        //----------------------------------

        //Получаем количество учеников по физкультурно-спортивным программам
        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->all();

        //var_dump(count($groups));
        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 12, count($participants));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 12, count($participants2));




        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();




        //$newParticipants = $participants;

        //var_dump($newParticipants);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 12, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(4, 12, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 12, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(6, 12, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(7, 12, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(8, 12, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(9, 12, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 12, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(11, 12, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(12, 12, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(13, 12, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(14, 12, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 12, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 12, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 12, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 12, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(3)->setCellValueByColumnAndRow(5, 12, count($participants2) - count($participants));

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

    static public function DownloadDod($start_date, $end_date)
    {

        $counter = 0;

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_1_DOD.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_1_DOD.xlsx');

        //----------------
        //--Раздел 3,4,5--
        //----------------

        //--Техническая направленность--

        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 1])])
            ->all();


        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();


        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $participants3 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingGroup.is_network' => 1])->all();

        $participants4 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingProgram.allow_remote_id' => 2])->all();

        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 8, count($participants));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 8, count($participants2));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 8, count($participants3));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 8, count($participants4));


        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();

        $arrParticipants = [
            ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01')
        ];


        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 6, $arrParticipants[0]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 6, $arrParticipants[1]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 6, $arrParticipants[2]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 6, $arrParticipants[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 6, $arrParticipants[4]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 6, $arrParticipants[5]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 6, $arrParticipants[6]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 6, $arrParticipants[7]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 6, $arrParticipants[8]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 6, $arrParticipants[9]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 6, $arrParticipants[10]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 6, $arrParticipants[11]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 6, $arrParticipants[12]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 6, $arrParticipants[13]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 6, $arrParticipants[14]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 6, array_sum($arrParticipants));


        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 6, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 6, count($participants2) - count($participants));

        //------------------------------

        //--Естественнонаучная направленность--

        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 4])])
            ->all();

        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $participants3 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingGroup.is_network' => 1])->all();

        $participants4 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingProgram.allow_remote_id' => 2])->all();

        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 9, count($participants));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 9, count($participants2));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 9, count($participants3));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 9, count($participants4));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();


        $arrParticipants1 = [
            ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01')
        ];


        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 7, $arrParticipants1[0]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 7, $arrParticipants1[1]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 7, $arrParticipants1[2]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 7, $arrParticipants1[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 7, $arrParticipants1[4]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 7, $arrParticipants1[5]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 7, $arrParticipants1[6]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 7, $arrParticipants1[7]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 7, $arrParticipants1[8]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 7, $arrParticipants1[9]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 7, $arrParticipants1[10]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 7, $arrParticipants1[11]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 7, $arrParticipants1[12]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 7, $arrParticipants1[13]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 7, $arrParticipants1[14]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 7, array_sum($arrParticipants1));

/*
        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 7, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 7, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 7, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 7, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 7, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 7, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 7, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 7, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 7, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 7, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 7, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 7, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 7, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 7, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 7, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01'));
*/

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 7, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 7, count($participants2) - count($participants));

        //-------------------------------------

        //--Социально-педагогическая направленность--

        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 3])])
            ->all();

        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $participants3 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingGroup.is_network' => 1])->all();

        $participants4 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingProgram.allow_remote_id' => 2])->all();

        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 11, count($participants));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 11, count($participants2));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 11, count($participants3));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 11, count($participants4));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();


        $arrParticipants2 = [
            ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01')
        ];


        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 9, $arrParticipants2[0]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 9, $arrParticipants2[1]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 9, $arrParticipants2[2]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 9, $arrParticipants2[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 9, $arrParticipants2[4]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 9, $arrParticipants2[5]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 9, $arrParticipants2[6]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 9, $arrParticipants2[7]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 9, $arrParticipants2[8]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 9, $arrParticipants2[9]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 9, $arrParticipants2[10]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 9, $arrParticipants2[11]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 9, $arrParticipants2[12]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 9, $arrParticipants2[13]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 9, $arrParticipants2[14]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 9, array_sum($arrParticipants2));

/*
        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 9, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 9, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 9, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 9, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 9, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 9, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 9, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 9, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 9, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 9, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 9, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 9, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 9, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 9, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 9, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01'));
*/

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 9, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 9, count($participants2) - count($participants));

        //------------------------------------------

        //--Художественная направленность--

        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 2])])
            ->all();

        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $participants3 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingGroup.is_network' => 1])->all();

        $participants4 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingProgram.allow_remote_id' => 2])->all();

        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 12, count($participants));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 12, count($participants2));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 12, count($participants3));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 12, count($participants4));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();


        $arrParticipants3 = [
            ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01')
        ];


        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 10, $arrParticipants3[0]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 10, $arrParticipants3[1]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 10, $arrParticipants3[2]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 10, $arrParticipants3[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 10, $arrParticipants3[4]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 10, $arrParticipants3[5]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 10, $arrParticipants3[6]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 10, $arrParticipants3[7]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 10, $arrParticipants3[8]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 10, $arrParticipants3[9]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 10, $arrParticipants3[10]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 10, $arrParticipants3[11]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 10, $arrParticipants3[12]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 10, $arrParticipants3[13]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 10, $arrParticipants3[14]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 10, array_sum($arrParticipants3));

/*
        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 10, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 10, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 10, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 10, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 10, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 10, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 10, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 10, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 10, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 10, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 10, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 10, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 10, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 10, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 10, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01'));
*/

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 10, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 10, count($participants2) - count($participants));

        //---------------------------------

        //--Физкультурно-спортивная направленность--

        $groupsId = [];

        $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['trainingProgram.focus_id' => 5])])
            ->all();


        $counter += count($groups);
        
        foreach ($groups as $group) $groupsId[] = $group->id;

        $participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->all();

        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();
        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['participant.sex' => 'Женский'])->all();

        $participants3 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingGroup.is_network' => 1])->all();

        $participants4 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['participant participant'])->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'participant_id', ExcelWizard::GetParticipantsIdsFromGroups($groupsId)])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->andWhere(['trainingProgram.allow_remote_id' => 2])->all();

        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 14, count($participants));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 14, count($participants2));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 14, count($participants3));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 14, count($participants4));

        //Делим учеников по возрастам

        $participantsId = [];
        foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $participantsId])->all();


        $arrParticipants4 = [
            ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01')
        ];


        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 12, $arrParticipants4[0]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 12, $arrParticipants4[1]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 12, $arrParticipants4[2]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 12, $arrParticipants4[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 12, $arrParticipants4[4]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 12, $arrParticipants4[5]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 12, $arrParticipants4[6]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 12, $arrParticipants4[7]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 12, $arrParticipants4[8]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 12, $arrParticipants4[9]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 12, $arrParticipants4[10]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 12, $arrParticipants4[11]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 12, $arrParticipants4[12]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 12, $arrParticipants4[13]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 12, $arrParticipants4[14]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 12, array_sum($arrParticipants4));

/*
        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 12, ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 12, ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 12, ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 12, ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 12, ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 12, ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 12, ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 12, ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 12, ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 12, ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 12, ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 12, ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 12, ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 12, ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 12, ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01'));
*/

        //--Считаем общее число учеников--

        $newParticipants = ForeignEventParticipantsWork::find()->all();

        $legalParticipants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['<=', 'trainingGroup.start_date', $end_date])->andWhere(['>=', 'trainingGroup.finish_date', $end_date])->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])->all();

        $pIds = [];
        foreach ($legalParticipants as $one) $pIds[] = $one->participant_id;

        $newParticipants = ForeignEventParticipantsWork::find()->where(['IN', 'id', $pIds])->all();

        $ageParts = [
            ExcelWizard::getParticipantsByAge(3, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(4, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(5, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(6, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(7, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(8, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(9, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(10, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(11, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(12, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(13, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(14, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(15, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(16, $newParticipants, substr($start_date, 0, 4).'-01-01'),
            ExcelWizard::getParticipantsByAge(17, $newParticipants, substr($start_date, 0, 4).'-01-01')

        ];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 16, $ageParts[0] + $ageParts[1] + $ageParts[2] + $ageParts[3] + $ageParts[4] + $ageParts[5] + $ageParts[6] + $ageParts[7] + $ageParts[8] + $ageParts[9] + $ageParts[10] + $ageParts[11] + $ageParts[12] + $ageParts[13] + $ageParts[14] + $ageParts[15]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 16, $ageParts[0]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 16, $ageParts[1]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 16, $ageParts[2]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 16, $ageParts[3]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 16, $ageParts[4]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 16, $ageParts[5]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16, $ageParts[6]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 16, $ageParts[7]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 16, $ageParts[8]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 16, $ageParts[9]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 16, $ageParts[10]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 16, $ageParts[11]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 16, $ageParts[12]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 16, $ageParts[13]);
        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 16, $ageParts[14]);



        //Старый вариант подсчета (если понадобится снова)
        /*$inputData->getSheet(1)->setCellValueByColumnAndRow(2, 16, 
            array_sum($arrParticipants) + array_sum($arrParticipants1) + array_sum($arrParticipants2) + 
            array_sum($arrParticipants3) + array_sum($arrParticipants4));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(4, 16, 
            $arrParticipants[0] + $arrParticipants1[0] + $arrParticipants2[0] + $arrParticipants3[0] + $arrParticipants4[0]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(5, 16, 
            $arrParticipants[1] + $arrParticipants1[1] + $arrParticipants2[1] + $arrParticipants3[1] + $arrParticipants4[1]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(6, 16, 
            $arrParticipants[2] + $arrParticipants1[2] + $arrParticipants2[2] + $arrParticipants3[2] + $arrParticipants4[2]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(7, 16, 
            $arrParticipants[3] + $arrParticipants1[3] + $arrParticipants2[3] + $arrParticipants3[3] + $arrParticipants4[3]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(8, 16, 
            $arrParticipants[4] + $arrParticipants1[4] + $arrParticipants2[4] + $arrParticipants3[4] + $arrParticipants4[4]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(9, 16, 
            $arrParticipants[5] + $arrParticipants1[5] + $arrParticipants2[5] + $arrParticipants3[5] + $arrParticipants4[5]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16, 
            $arrParticipants[6] + $arrParticipants1[6] + $arrParticipants2[6] + $arrParticipants3[6] + $arrParticipants4[6]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(11, 16, 
            $arrParticipants[7] + $arrParticipants1[7] + $arrParticipants2[7] + $arrParticipants3[7] + $arrParticipants4[7]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(12, 16, 
            $arrParticipants[8] + $arrParticipants1[8] + $arrParticipants2[8] + $arrParticipants3[8] + $arrParticipants4[8]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(13, 16, 
            $arrParticipants[9] + $arrParticipants1[9] + $arrParticipants2[9] + $arrParticipants3[9] + $arrParticipants4[9]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(14, 16, 
            $arrParticipants[10] + $arrParticipants1[10] + $arrParticipants2[10] + $arrParticipants3[10] + $arrParticipants4[10]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(15, 16, 
            $arrParticipants[11] + $arrParticipants1[11] + $arrParticipants2[11] + $arrParticipants3[11] + $arrParticipants4[11]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(16, 16, 
            $arrParticipants[12] + $arrParticipants1[12] + $arrParticipants2[12] + $arrParticipants3[12] + $arrParticipants4[12]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(17, 16, 
            $arrParticipants[13] + $arrParticipants1[13] + $arrParticipants2[13] + $arrParticipants3[13] + $arrParticipants4[13]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(18, 16, 
            $arrParticipants[14] + $arrParticipants1[14] + $arrParticipants2[14] + $arrParticipants3[14] + $arrParticipants4[14]);*/


        //--------------------------------

        //Добавляем детей по финансированию
        $participants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 1])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 12, count($participants));

        $participants2 = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['IN', 'trainingGroup.id', $groupsId])
            ->andWhere(['IN', 'participant_id', ExcelWizard::CheckParticipant18Plus($newParticipants, substr($start_date, 0, 4).'-01-01')])
            ->all();

        
        $ids = [];
        foreach ($participants2 as $p) $ids[] = $p->participant_id;
        $tempP = ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
        //foreach ($tempP as $p) echo $p->fullName.'<br>';
        

        //$participants = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', $groupsId])->andWhere(['trainingGroup.budget' => 0])->all();

        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 12, count($participants2) - count($participants));

        //------------------------------------------

        //-------------
        //--Раздел 10--
        //-------------

        //--Получаем все помещения--

        $audsAll = AuditoriumWork::find()/*->where(['branch_id' => 3])*/;

        //--Получаем все помещения не в собственности--

        $audsRent = AuditoriumWork::find()->where(['!=', 'branch_id', 3]);

        //----Ищем лаборатории--

        $labs = (clone $audsAll)->andWhere(['auditorium_type_id' => 1])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 13, count($labs) > 0 ? 1 : 2);

        $labs = (clone $audsRent)->andWhere(['auditorium_type_id' => 1])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 13, count($labs) > 0 ? 1 : 2);

        //----------------------

        //----Ищем мастерские--

        $work = (clone $audsAll)->andWhere(['auditorium_type_id' => 2])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 14, count($work) > 0 ? 1 : 2);

        $work = (clone $audsRent)->andWhere(['auditorium_type_id' => 2])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 14, count($work) > 0 ? 1 : 2);

        //---------------------

        //----Ищем учебные классы--

        $stud = (clone $audsAll)->andWhere(['auditorium_type_id' => 3])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 12, count($stud) > 0 ? 1 : 2);

        $stud = (clone $audsRent)->andWhere(['auditorium_type_id' => 3])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 12, count($stud) > 0 ? 1 : 2);

        //-------------------------

        //----Ищем лекционные аудитории--

        $lec = (clone $audsAll)->andWhere(['auditorium_type_id' => 4])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 20, count($lec) > 0 ? 1 : 2);

        $lec = (clone $audsRent)->andWhere(['auditorium_type_id' => 4])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 20, count($lec) > 0 ? 1 : 2);

        //-------------------------------

        //----Ищем компьютерные кабинеты--

        $lec = (clone $audsAll)->andWhere(['auditorium_type_id' => 5])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 21, count($lec) > 0 ? 1 : 2);

        $lec = (clone $audsRent)->andWhere(['auditorium_type_id' => 5])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 21, count($lec) > 0 ? 1 : 2);

        //--------------------------------

        //----Ищем актовые залы--

        $lec = (clone $audsAll)->andWhere(['auditorium_type_id' => 6])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 8, count($lec) > 0 ? 1 : 2);

        $lec = (clone $audsRent)->andWhere(['auditorium_type_id' => 6])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 8, count($lec) > 0 ? 1 : 2);

        //-----------------------


        //-------------
        //--Раздел 11--
        //-------------

        //--Получаем все аудитории--

        $auds = AuditoriumWork::find()->where(['include_square' => 1]);

        //--Считаем площадь помещений--

        $sumArea = 0.0;
        $sumStudyArea = 0.0;
        $sumRentArea = 0.0;
        $sumRentStudyArea = 0.0;
        $sumOperationArea = 0.0;
        $sumOperationStudyArea = 0.0;

        $audsAll = (clone $auds)->all();
        foreach ($audsAll as $aud)
        {
            $sumArea += $aud->square;
            if ($aud->is_education == 1) $sumStudyArea += $aud->square;

            if ($aud->branch_id == 3 || $aud->branch_id == 8)
            {
                $sumOperationArea += $aud->square;
                if ($aud->is_education == 1) $sumOperationStudyArea += $aud->square;
            }
            else /*if ($aud->branch_id == 1 || $aud->branch_id == 2)*/
            {
                $sumRentArea += $aud->square;
                if ($aud->is_education == 1) $sumRentStudyArea += $aud->square;
            }

        }

        $inputData->getSheet(4)->setCellValueByColumnAndRow(2, 8, $sumArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(5, 8, $sumOperationArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(6, 8, $sumRentArea);

        $inputData->getSheet(4)->setCellValueByColumnAndRow(2, 9, $sumStudyArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(5, 9, $sumOperationStudyArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(6, 9, $sumRentStudyArea);

        //--------------------------



        //----------------------------------------------------------




        //-----------------------------------------

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

    static private function GetParticipantsByAge1($age, $participants, $date)
    {
        $participantsId = [];
        foreach ($participants as $participant){
            if (round(floor((strtotime($date) - strtotime($participant->birthdate))) / (60 * 60 * 24 * 365.25)) == $age)
                $participantsId[] = $participant->id;
        }
        return $participantsId;
    }

    static public function CheckParticipant18Plus($participants, $date)
    {
        $participantsId = [];
        foreach ($participants as $participant){
            if (round(floor((strtotime($date) - strtotime($participant->birthdate))) / (60 * 60 * 24 * 365.25)) >= 3 && round(floor((strtotime($date) - strtotime($participant->birthdate))) / (60 * 60 * 24 * 365.25)) <= 17)
                $participantsId[] = $participant->id;
        }
        return $participantsId;
    }

    static private function GetParticipantsByAgeRange($age_left, $age_right, $participants, $date)
    {
        $participantsId = [];
        foreach ($participants as $participant){
            if (round(floor((strtotime($date) - strtotime($participant->birthdate))) / (60 * 60 * 24 * 365.25)) >= $age_left && round(floor((strtotime($date) - strtotime($participant->birthdate))) / (60 * 60 * 24 * 365.25)) <= $age_right)
                $participantsId[] = $participant->id;
        }
        return count($participantsId);
    }

    static public function GetGroupsByBranchAndFocus($branch_id, $focus_id, $budget = null)
    {
        $programs = TrainingProgramWork::find()->where(['IN', 'focus_id', $focus_id])->all();
        if ($focus_id == 0)
        {
            $programs = TrainingProgramWork::find()->all();
        }
        $tpIds = [];
        foreach ($programs as $program) $tpIds[] = $program->id;
        
        if ($budget === null)
        {
            $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'trainingProgram.id', $tpIds])->andWhere(['branch_id' => $branch_id])->andWhere(['budget' => 1])->all();
        }
        else
        {
            $groups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'trainingProgram.id', $tpIds])->andWhere(['branch_id' => $branch_id])->andWhere(['IN', 'budget', $budget])->all();
        }

        
        $gIds = [];
        foreach ($groups as $group) $gIds[] = $group->id;


        return $gIds;
    }

    static public function GetGroupsByDatesBranchFocus($start_date, $end_date, $branch_id, $focus_id, $budget = null)
    {
        /*$groups = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();
        */
        $groups = TrainingGroupWork::find()->where(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->andWhere(['IN', 'id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id, $budget)])
            ->all();
        
        $gIds = [];
        
        foreach ($groups as $group) $gIds[] = $group->id;
        return $gIds;

        /*
        $gIds = [];
        foreach ($groups as $group) $gIds[] = $group->training_group_id;

        if (count($gIds) > 0)
        {
            $resGroups = TrainingGroupWork::find()->where(['IN', 'id', $gIds])->all();
            
            $res = [];
            foreach ($resGroups as $group) $res[] = $group->id;
            return $res;
        }
        else
            return [];
        */
    }


    static public function GetSchooltechProjectSuccess($start_date, $end_date, $branch_id, $focus_id, $allow_remote_id)
    {
        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')
            ->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->all();

        $result = 0;
        $allParts = 0;
        foreach ($trainingGroups as $group)
        {

            if ($group->branch_id == $branch_id && $group->trainingProgram->focus_id == $focus_id)
            {
                $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
                foreach ($parts as $part)
                {
                    if ($part->group_project_themes_id !== null)
                        $result += 1;
                    $allParts += 1;
                }
            }


        }

        return [$result, $allParts];
    }

    //получаем процент победителей и призеров от общего числа участников
    static public function GetPercentEventParticipants($start_date, $end_date, $branch_id, $focus_id, $budget, $allow_remote = 1)
    {
        $winners1 = ExcelWizard::GetPrizesWinners(8, 0, 0, $start_date, $end_date, $branch_id, $focus_id, [], $allow_remote);
        $winners2 = ExcelWizard::GetPrizesWinners(7, 0, 0, $start_date, $end_date, $branch_id, $focus_id, [], $allow_remote);
        $winners3 = ExcelWizard::GetPrizesWinners(6, 0, 0, $start_date, $end_date, $branch_id, $focus_id, [], $allow_remote);

        if ($branch_id == 4 && $focus_id == 1)
            $extraParts = ExcelWizard::GetSchooltechProjectSuccess($start_date, $end_date, $branch_id, $focus_id, $allow_remote);

        //if ($branch_id == 1)
        //   var_dump($winners1[0] + $winners1[1] + $winners2[0] + $winners2[1] + $winners3[0] + $winners3[1]);
        $all = ExcelWizard::GetAllParticipantsForeignEvents(8, 0, 0, $start_date, $end_date, $branch_id, $focus_id, $allow_remote) +
            ExcelWizard::GetAllParticipantsForeignEvents(7, 0, 0, $start_date, $end_date, $branch_id, $focus_id, $allow_remote) +
            ExcelWizard::GetAllParticipantsForeignEvents(6, 0, 0, $start_date, $end_date, $branch_id, $focus_id, $allow_remote);

        //var_dump($all);

        if ($branch_id == 4 && $focus_id == 1)
        {
            if ($winners1[4] + $winners2[4] + $winners3[4] + $extraParts[1] == 0) return 0;
        }
        else
        if ($winners1[4] + $winners2[4] + $winners3[4] == 0) return 0;


        if ($branch_id == 4 && $focus_id == 1)
        {
            return round(($winners1[1] + $winners2[1] + $winners3[1] + $winners1[0] + $winners2[0] + $winners3[0] + $extraParts[0]) /
                ($winners1[4] + $winners2[4] + $winners3[4] + $extraParts[1]) * 100);
        }
        else
            return round(($winners1[1] + $winners2[1] + $winners3[1] + $winners1[0] + $winners2[0] + $winners3[0]) / ($winners1[4] + $winners2[4] + $winners3[4]) * 100);




        //return round((($winners1[0] + $winners1[1] + $winners2[0] + $winners2[1] + $winners3[0] + $winners3[1]) / $all) * 100);
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



        /*if ($branch_id == 7 && $focus_id == 2)
        {
            $newParts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();

            foreach ($newParts as $part)
                echo ForeignEventParticipantsWork::find()->where(['id' => $part->participant_id])->one()->fullName.'|'.TrainingGroupWork::find()->where(['id' => $part->training_group_id])->one()->number.'<br>';
        }*/

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
            ->andWhere(['>', 'LENGTH(`certificat_number`)', 1])
            ->all();

        $pIds = [];
        foreach ($projectParts as $one) $pIds[] = $one->id;

        $projectParts1 = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->andWhere(['NOT IN', 'training_group_participant.id', $pIds])
            ->all();

        
        $p1Ids = [];
        foreach ($projectParts1 as $one) $p1Ids[] = $one->id;



        $newCertificats = CertificatWork::find()->where(['IN', 'training_group_participant_id', $p1Ids])->all();



        $allParts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->select('participant_id')->where(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])])
            ->orWhere(['IN', 'trainingGroup.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.id', ExcelWizard::GetGroupsByBranchAndFocus($branch_id, $focus_id)])
            ->all();

        if (count($projectParts) == 0 && count($newCertificats) == 0) return 0;
        return round(((count($projectParts) + count($newCertificats)) / count($allParts)) * 100);
    }

    static public function GetAllParticipantsFromProgram($start_date, $end_date, $training_program, $unic)
    {
        $trainingGroups = TrainingGroupWork::find()->joinWith(['trainingProgram trainingProgram'])->where(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['<', 'start_date', $end_date])->andWhere(['trainingProgram.id' => $training_program])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['>', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['<', 'start_date', $start_date])->andWhere(['>', 'finish_date', $end_date])->andWhere(['trainingProgram.id' => $training_program])])
            ->orWhere(['IN', 'training_group.id', (new Query())->select('training_group.id')->from('training_group')->where(['>', 'start_date', $start_date])->andWhere(['<', 'finish_date', $end_date])->andWhere(['trainingProgram.id' => $training_program])])
            ->all();

        $tgIds = [];
        foreach ($trainingGroups as $group) $tgIds[] = $group->id;

        if ($unic == 0)
            $parts = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $tgIds])->all();
        else
            $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $tgIds])->all();

        return count($parts);
    }

    static public function DownloadGZ($start_date, $end_date, $visit_flag)
    {
        ini_set('max_execution_time', '6000');
        ini_set('memory_limit', '2048M');
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
        
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 39, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 4, 1, 1, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 39)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 39)->getStyle()->getAlignment()->setHorizontal('center');

                //--------------------------------------

                //Отдел ЦОД (естес.-науч. направленность)

                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 49, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 7, 4));
                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 51, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 7, 4));
                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 52, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 7, 4, 1));
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 49)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 49)->getStyle()->getAlignment()->setHorizontal('center');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 51)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 51)->getStyle()->getAlignment()->setHorizontal('center');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 52)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 52)->getStyle()->getAlignment()->setHorizontal('center');

                //--------------------------------------

                //Отдел ЦОД (худож. направленность)

                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 54, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 7, 2));
                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 56, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 7, 2));
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 54)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 54)->getStyle()->getAlignment()->setHorizontal('center');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 56)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 56)->getStyle()->getAlignment()->setHorizontal('center');

                //--------------------------------------

                //Отдел ЦОД (тех. направленность - очная)

                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 41, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 7, 1));
                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 43, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 7, 1));
                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 44, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 7, 1, 1));
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 43)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 43)->getStyle()->getAlignment()->setHorizontal('center');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 44)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 44)->getStyle()->getAlignment()->setHorizontal('center');

                //--------------------------------------

                //Отдел ЦОД (тех. направленность - очная с дистантом)

                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 48, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 7, 1, 1, 2));


                $inputData->getSheet(1)->getCellByColumnAndRow(10, 48)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 48)->getStyle()->getAlignment()->setHorizontal('center');

                //---------------------------------------------------

                //Отдел ЦОД (физкул.-спортивная направленность)

                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 58, ExcelWizard::GetPercentDoubleParticipant($start_date, $end_date, 7, 5));
                $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 60, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 7, 5, 1));
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 58)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 58)->getStyle()->getAlignment()->setHorizontal('center');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 60)->getStyle()->getAlignment()->setVertical('top');
                $inputData->getSheet(1)->getCellByColumnAndRow(10, 60)->getStyle()->getAlignment()->setHorizontal('center');

                //---------------------------------------------

                //-----------------------------------------------------

                //Кол-во человеко-часов

                $statusArr = [];
                if ($visit_flag == 1) $statusArr = [0, 1, 2];
                else $statusArr = [0, 2];


                //Отдел Технопарк (тех. направленность)


                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 2, 1)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();


                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 8, count($visits));

                //---------------

                //Отдел ЦДНТТ (тех. направленность)

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 1)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                //$visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 1)])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 9, count($visits));

                //---------------

                //Отдел ЦДНТТ (худ. направленность)

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 2)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 10, count($visits));

                //---------------

                //Отдел ЦДНТТ (соц-пед. направленность)

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 3, 3)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 11, count($visits));

                //---------------

                //Отдел Кванториум (тех. направленность)

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 1, 0)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 12, count($visits));

                //---------------

                //Отдел Моб. Кванториум (тех. направленность)

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 4, 1)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 13, count($visits));

                //---------------

                //Отдел ЦОД (тех. направленность - очная)

                $gIds = [];
                $tpIds = [];
                $tps = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->andWhere(['IN', 'trainingProgram.allow_remote_id', [0, 1]])->all();
                foreach ($tps as $tp) $tpIds[] = $tp->training_program_id;
                $groups = TrainingGroupWork::find()->where(['IN', 'training_program_id', $tpIds])->all();
                foreach ($groups as $group) $gIds[] = $group->id;

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 1)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'trainingGroupLesson.training_group_id', $gIds])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 14, count($visits));

                //---------------

                //Отдел ЦОД (тех. направленность - дистант)

                $gIds = [];
                $tpIds = [];
                $tps = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->andWhere(['IN', 'trainingProgram.allow_remote_id', [2]])->all();
                foreach ($tps as $tp) $tpIds[] = $tp->training_program_id;
                $groups = TrainingGroupWork::find()->where(['IN', 'training_program_id', $tpIds])->all();
                foreach ($groups as $group) $gIds[] = $group->id;

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 1)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'trainingGroupLesson.training_group_id', $gIds])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 15, count($visits));

                //---------------

                //Отдел ЦОД (естес.-науч. направленность)

                $gIds = [];
                $tpIds = [];
                $tps = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->andWhere(['IN', 'trainingProgram.allow_remote_id', [0, 1]])->all();
                foreach ($tps as $tp) $tpIds[] = $tp->training_program_id;
                $groups = TrainingGroupWork::find()->where(['IN', 'training_program_id', $tpIds])->all();
                foreach ($groups as $group) $gIds[] = $group->id;

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 4)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'trainingGroupLesson.training_group_id', $gIds])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 16, count($visits));

                //---------------

                //Отдел ЦОД (худож. направленность)

                $gIds = [];
                $tpIds = [];
                $tps = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->andWhere(['IN', 'trainingProgram.allow_remote_id', [0, 1]])->all();
                foreach ($tps as $tp) $tpIds[] = $tp->training_program_id;
                $groups = TrainingGroupWork::find()->where(['IN', 'training_program_id', $tpIds])->all();
                foreach ($groups as $group) $gIds[] = $group->id;

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 2)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'trainingGroupLesson.training_group_id', $gIds])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 17, count($visits));

                //---------------

                //Отдел ЦОД (физкульт.-спорт. направленность)

                $gIds = [];
                $tpIds = [];
                $tps = BranchProgramWork::find()->joinWith(['trainingProgram trainingProgram'])->andWhere(['IN', 'trainingProgram.allow_remote_id', [0, 1]])->all();
                foreach ($tps as $tp) $tpIds[] = $tp->training_program_id;
                $groups = TrainingGroupWork::find()->where(['IN', 'training_program_id', $tpIds])->all();
                foreach ($groups as $group) $gIds[] = $group->id;

                $visits = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['IN', 'trainingGroupLesson.training_group_id', ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, 7, 5)])->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->andWhere(['IN', 'visit.id', (new Query())->select('visit.id')->from('visit')->where(['IN', 'status', $statusArr])])->all();

                $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 18, count($visits));

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

    static public function GetParticipantsFromGroup($training_group_ids, $sex)
    {
        $result = [];
        if (count($training_group_ids) > 0)
            $result = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $training_group_ids])->andWhere(['IN', 'participant.sex', $sex])->all();


        $resIds = [];
        foreach ($result as $one) $resIds[] = $one->participant_id;

        $partsRes = ForeignEventParticipantsWork::find()->where(['IN', 'id', $resIds])->all();
        
        return $partsRes;
    }

    static public function GetParticipantsFromGroupAll($training_group_ids, $sex)
    {
        $result = [];
        if (count($training_group_ids) > 0)
            $result = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $training_group_ids])->andWhere(['IN', 'participant.sex', $sex])->all();

        if (count($result) > 0)
            return $result;
        else
            return [];
    }

    static public function GetParticipantsFromGroupDistinct($training_group_ids, $sex)
    {
        $result = [];
        if (count($training_group_ids) > 0)
            $result = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $training_group_ids])->andWhere(['IN', 'participant.sex', $sex])->all();

        $resIds = [];
        foreach ($result as $one)
        {
            //var_dump(count(TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $training_group_ids])->andWhere(['IN', 'participant.sex', $sex])->andWhere(['participant_id' => $one->participant_id])->all()));
            if (count(TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $training_group_ids])->andWhere(['IN', 'participant.sex', $sex])->andWhere(['participant_id' => $one->participant_id])->all()) > 1)
                $resIds[] = $one->participant_id;
        }

        $partsRes = ForeignEventParticipantsWork::find()->where(['IN', 'id', $resIds])->all();

        return $partsRes;
    }


    static public function DownloadDO($start_date, $end_date)
    {
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_DO.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_DO.xlsx');

        //получаем количество групп по направленностям

        $branchs = BranchWork::find()->all();
        $focuses = FocusWork::find()->all();
        $sumArr = [];  
        $sumArrCom = []; 
        $allGroups = [];
        $allGroupsCom = [];

        foreach ($focuses as $focus)
        {
            $sum = 0;
            $sumCom = 0;
            $groupsId = [];
            $groupsIdCom = [];
            foreach ($branchs as $branch) 
            {
                $groups = ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, $branch->id, $focus->id, [0, 1]);
                foreach ($groups as $group) $groupsId[] = $group;

                $sum += count($groups);

                $groupsCom = ExcelWizard::GetGroupsByDatesBranchFocus($start_date, $end_date, $branch->id, $focus->id, [0]);
                foreach ($groupsCom as $group) $groupsIdCom[] = $group;

                $sumCom += count($groupsCom);
            }
            $allGroups[] = $groupsId;
            $allGroupsCom[] = $groupsIdCom;
            $sumArr[] = $sum;
            $sumArrCom[] = $sumCom;
        }

        //var_dump($allGroups[0]);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 21, $sumArr[0] + $sumArr[1] + $sumArr[2] + $sumArr[3] + $sumArr[4]);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 21, $sumArr[0] + $sumArr[1] + $sumArr[2] + $sumArr[3] + $sumArr[4]);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 30, $sumArrCom[0] + $sumArrCom[1] + $sumArrCom[2] + $sumArrCom[3] + $sumArrCom[4]);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 30, $sumArrCom[0] + $sumArrCom[1] + $sumArrCom[2] + $sumArrCom[3] + $sumArrCom[4]);

        //техническая направленность

        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 22, $sumArr[0]);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 22, $sumArr[0]);

        //--------------------------

        //художественная направленность

        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 27, $sumArr[1]);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 27, $sumArr[1]);

        //-----------------------------

        //социально-педагогическая направленность + естественнонаучная направленность

        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 29, $sumArr[2] + $sumArr[3]);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 29, $sumArr[2] + $sumArr[3]);

        //----------------------------------------------------------------------------

        //физкультурно-спортивная направленность

        $inputData->getSheet(2)->setCellValueByColumnAndRow(15, 26, $sumArr[4]);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(16, 26, $sumArr[4]);

        //--------------------------------------

        //--------------------------------------------

        //получаем количество детей по технической направленности

        $allParts = 0;
        $allPartsDouble = 0;
        $allPartsCom = 0;
        $allPartsDoubleCom = 0;

        if ($allGroups[0] !== null)
        {
            $temp = count(ExcelWizard::GetParticipantsFromGroupAll($allGroups[0], ['Мужской', 'Женский']));
            $temp1 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[0], ['Мужской', 'Женский']));
            $temp2 = count(ExcelWizard::GetParticipantsFromGroupAll($allGroupsCom[0], ['Мужской', 'Женский']));
            $temp3 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroupsCom[0], ['Мужской', 'Женский']));
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 22, $temp);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(18, 22, $temp1);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(19, 22, $temp);


            //$temp = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[0], ['Мужской', 'Женский']));
            
            $allParts += $temp;
            $allPartsDouble += $temp1;
            $allPartsCom += $temp2;
            $allPartsDoubleCom += $temp3;
        }
        else
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 22, 0);

        //-------------------------------------------------------

        //получаем количество детей по художественной направленности

        if ($allGroups[1] !== null)
        {
            $sex = ['Мужской', 'Женский'];
            $temp = count(ExcelWizard::GetParticipantsFromGroupAll($allGroups[1], $sex));
            $temp1 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[1], $sex));
            $temp2 = count(ExcelWizard::GetParticipantsFromGroupAll($allGroupsCom[1], $sex));
            $temp3 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroupsCom[1], $sex));
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 27, $temp);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(18, 27, $temp1);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(19, 27, $temp);

            //$temp = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[1], ['Мужской', 'Женский']));
            
            $allParts += $temp;
            $allPartsDouble += $temp1;
            $allPartsCom += $temp2;
            $allPartsDoubleCom += $temp3;
        }
        else
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 27, 0);

        

        //----------------------------------------------------------

        //получаем количество детей по социально-педагогической направленности + естественнонаучной направленности

        if ($allGroups[3] !== null)
        {
            foreach ($allGroups[3] as $group) $allGroups[3][] = $group;
            $temp = count(ExcelWizard::GetParticipantsFromGroupAll($allGroups[3], ['Мужской', 'Женский']));
            $temp1 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[3], ['Мужской', 'Женский']));
            $temp2 = count(ExcelWizard::GetParticipantsFromGroupAll($allGroupsCom[3], ['Мужской', 'Женский']));
            $temp3 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroupsCom[3], ['Мужской', 'Женский']));
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 29, $temp);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(18, 29, $temp1);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(19, 29, $temp);

            //$temp = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[3], ['Мужской', 'Женский']));
           
            $allParts += $temp;
            $allPartsDouble += $temp1;
            $allPartsCom += $temp2;
            $allPartsDoubleCom += $temp3;
        }
        else
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 29, 0);
        

        //----------------------------------------------------------

        //получаем количество детей по физкультурно-спортивной направленности

        if ($allGroups[4] !== null)
        {
            $sex = ['Мужской', 'Женский'];
            $temp = count(ExcelWizard::GetParticipantsFromGroupAll($allGroups[4], $sex));
            $temp1 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[4], $sex));
            $temp2 = count(ExcelWizard::GetParticipantsFromGroupAll($allGroupsCom[4], $sex));
            $temp3 = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroupsCom[4], $sex));
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 26, $temp);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(18, 26, $temp1);
            $inputData->getSheet(2)->setCellValueByColumnAndRow(19, 26, $temp);

            //$temp = count(ExcelWizard::GetParticipantsFromGroupDistinct($allGroups[4], ['Мужской', 'Женский']));
            
            $allParts += $temp;
            $allPartsDouble += $temp1;
            $allPartsCom += $temp2;
            $allPartsDoubleCom += $temp3;
        }
        else
            $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 26, 0);

        

        //----------------------------------------------------------

        

        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 21, $allParts);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(18, 21, $allPartsDouble/* + $allPartsDoubleCom*/);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(19, 21, $allParts);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(17, 30, $allPartsCom);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(18, 30, $allPartsDoubleCom);
        $inputData->getSheet(2)->setCellValueByColumnAndRow(19, 30, $allPartsCom);

        $newAllGroups = [];
        foreach ($allGroups as $group) $newAllGroups = array_merge($newAllGroups, $group);


        //получаем количество детей по возрасту

        $date = explode("-", $start_date)[0];
        $date .= '-01-01';
        $sum = 0;
        $tempS = 0;

        $pg = ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Мужской', 'Женский']);


        $tempS = ExcelWizard::GetParticipantsByAgeRange(0, 4, $pg, $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(15, 21, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(5, 9, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Мужской', 'Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(15, 22, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(10, 14, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Мужской', 'Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(15, 23, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(15, 17, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Мужской', 'Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(15, 24, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(18, 99, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Мужской', 'Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(15, 25, $tempS);
        $sum += $tempS;

        $inputData->getSheet(5)->setCellValueByColumnAndRow(15, 26, $sum);

        $sum = 0;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(0, 4, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(16, 21, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(5, 9, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(16, 22, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(10, 14, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(16, 23, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(15, 17, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(16, 24, $tempS);
        $sum += $tempS;

        $tempS = ExcelWizard::GetParticipantsByAgeRange(18, 99, ExcelWizard::GetParticipantsFromGroup($newAllGroups, ['Женский']), $date);
        $inputData->getSheet(5)->setCellValueByColumnAndRow(16, 25, $tempS);
        $sum += $tempS;

        $inputData->getSheet(5)->setCellValueByColumnAndRow(16, 26, $sum);

        //-------------------------------------
        

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    static private function GetParticipantsByMonth($training_group_id, $year)
    {
        
        $group = TrainingGroupWork::find()->where(['id' => $training_group_id])->one();

        $orders = OrderGroupWork::find()->joinWith(['documentOrder documentOrder'])->where(['training_group_id' => $training_group_id])->orderBy(['documentOrder.order_date' => SORT_ASC])->all();

        $start_date = $group->start_date;
        $end_date = $group->finish_date;

        $ordersId = [];
        foreach ($orders as $order) $ordersId[] = $order->document_order_id;
        
        $month = [];
        $yearDistance = ((explode("-", $end_date)[0]) * 1) - ((explode("-", $start_date)[0]) * 1);

        $startYear = explode("-", $start_date)[0];
        $startMonth = (explode("-", $start_date)[1]) * 1;
        $endMonth = (explode("-", $end_date)[1]) * 1;

        $yearCounter = 0;
        while ($yearCounter < $yearDistance + 1)
        {
            $currentMonth = $yearCounter == 0 ? $startMonth : 1;
            while ($currentMonth < 13 && !($currentMonth - 1 == $endMonth && $yearCounter == $yearDistance))
            {
                $strMonth = $currentMonth > 9 ? $currentMonth : '0'.$currentMonth;
                $month[] = ($startYear + $yearCounter).'-'.$strMonth;
                $currentMonth++;
            }
            $yearCounter++;
        }

        $participantsCount = [];

        $orders = TrainingGroupParticipantWork::find()->where(['training_group_id' => $training_group_id])->all();
        $ogIds = [];
        foreach ($orders as $order) $ogIds[] = $order->id;
        

        $temp = 0;
        $exception = 0; // необходимо для граничных состояний, когда отчисление по группе и её завершение выпало на один месяц
        for ($i = 0; $i < count($month); $i++)
        {
            if ($i == 0)
                $pasta = OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->joinWith(['orderGroup.documentOrder documentOrder'])->where(['IN', 'group_participant_id', $ogIds])->andWhere(['<=', 'documentOrder.order_date', $month[$i].'-31'])->all();
            else if ($i == count($month) - 1)
                $pasta = OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->joinWith(['orderGroup.documentOrder documentOrder'])->where(['IN', 'group_participant_id', $ogIds])->andWhere(['>=', 'documentOrder.order_date', $month[$i].'-01'])->andWhere(['<', 'documentOrder.order_date', $month[$i].'-'.$end_date])->all();
            else
                $pasta = OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->joinWith(['orderGroup.documentOrder documentOrder'])->where(['IN', 'group_participant_id', $ogIds])->andWhere(['>=', 'documentOrder.order_date', $month[$i].'-01'])->andWhere(['<=', 'documentOrder.order_date', $month[$i].'-31'])->all();

            foreach ($pasta as $makaroni)
            {
                if ($makaroni->status == 0) $temp++;
                if ((count($month) == 1) && ($makaroni->status == 2 || $makaroni->orderGroup->documentOrder->study_type == 2 || $makaroni->orderGroup->documentOrder->study_type == 3)) $temp--;
                else if (($makaroni->status == 1 || $makaroni->status == 2) && count($month) != 1) $temp--;
                if ($i == count($month)-1 && ($makaroni->orderGroup->documentOrder->study_type == 0 || $makaroni->orderGroup->documentOrder->study_type == 1) && ($makaroni->status == 1 || $makaroni->status == 2) && count($month) > 1) $exception++;
            }

            if ($exception != 0)
            {
                $participantsCount[] = $exception;
                $exception = 0;
            }
            else
                $participantsCount[] = $temp;
        }

        $resParts = [];
        for ($i = 0; $i < count($month); $i++)
            if (explode("-", $month[$i])[0] == $year)
                $resParts[] = $participantsCount[$i];


        return $participantsCount;
    }

    static public function DownloadTeacher($year, $branch)
    {
        
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/template_Teacher.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/template_Teacher.xlsx');

        //получаем количество групп по направленностям

        $start_date = $year.'-01-01';
        $end_date = ($year + 1).'-01-01';


        $teachers = TeacherGroupWork::find()->select('teacher_id')->distinct()->where(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->andWhere(['IN', 'training_group_id', ExcelWizard::GetGroupsByBranchAndFocus($branch, 0, [0, 1])])
            ->all();

        

        $akaIds = [];
        foreach ($teachers as $teacher) $akaIds[] = $teacher->teacher_id;

        //var_dump($akaIds);

        $teachersPeople = PeopleWork::find()->where(['IN', 'id', $akaIds])->all();

        //var_dump(count($teachersPeople));

        $currentRow = 2;
        $tempCurrentRow = 5;
        
        $styleArray = array('fill'    => array(
                    'type'      => 'solid',
                    'color'     => array('rgb' => 'FFFFFF')
                ),
                    'borders' => array(
                        'bottom'    => array('style' => 'thin'),
                        'right'     => array('style' => 'thin'),
                        'top'     => array('style' => 'thin'),
                        'left'     => array('style' => 'thin')

                    )
                );

        foreach ($teachersPeople as $teacher)
        {
            $currentColumn = 0;
            $currentMonth = 1;
            $dayMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            $strMonth = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

            $inputData->getSheet(0)->mergeCells('A'.$currentRow.':Z'.$currentRow);
            $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, $teacher->fullName);
            $inputData->getSheet(0)->getStyle('A'.$currentRow.':Z'.$currentRow)->applyFromArray($styleArray);

            $currentRow++;

            $inputData->getSheet(0)->getStyle('A'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('B'.$currentRow.':C'.$currentRow);
            $inputData->getSheet(0)->getStyle('B'.$currentRow.':C'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('D'.$currentRow.':E'.$currentRow);
            $inputData->getSheet(0)->getStyle('D'.$currentRow.':E'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('F'.$currentRow.':G'.$currentRow);
            $inputData->getSheet(0)->getStyle('F'.$currentRow.':G'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('H'.$currentRow.':I'.$currentRow);
            $inputData->getSheet(0)->getStyle('H'.$currentRow.':I'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('J'.$currentRow.':K'.$currentRow);
            $inputData->getSheet(0)->getStyle('J'.$currentRow.':K'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('L'.$currentRow.':M'.$currentRow);
            $inputData->getSheet(0)->getStyle('L'.$currentRow.':M'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('N'.$currentRow.':O'.$currentRow);
            $inputData->getSheet(0)->getStyle('N'.$currentRow.':O'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('P'.$currentRow.':Q'.$currentRow);
            $inputData->getSheet(0)->getStyle('P'.$currentRow.':Q'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('R'.$currentRow.':S'.$currentRow);
            $inputData->getSheet(0)->getStyle('R'.$currentRow.':S'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('T'.$currentRow.':U'.$currentRow);
            $inputData->getSheet(0)->getStyle('T'.$currentRow.':U'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('V'.$currentRow.':W'.$currentRow);
            $inputData->getSheet(0)->getStyle('V'.$currentRow.':W'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->mergeCells('X'.$currentRow.':Y'.$currentRow);
            $inputData->getSheet(0)->getStyle('X'.$currentRow.':Y'.$currentRow)->applyFromArray($styleArray);
            $inputData->getSheet(0)->getStyle('Z'.$currentRow)->applyFromArray($styleArray);

            $currentColumn++;
            for ($i = 0; $i < 24; $i += 2, $currentColumn++)
                $inputData->getSheet(0)->setCellValueByColumnAndRow($i + 1, $currentRow, $strMonth[$currentColumn - 1].' '.$year);

            $inputData->getSheet(0)->setCellValueByColumnAndRow(25, $currentRow, 'ИТОГО');
            $currentRow++;

            $currentColumn = 0;
            $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, 'Группа');
            $inputData->getSheet(0)->getStyleByColumnAndRow($currentColumn, $currentRow)->applyFromArray($styleArray);
            for ($i = 1; $i < 25; $i++)
            {
                if ($i % 2 == 0)
                    $inputData->getSheet(0)->setCellValueByColumnAndRow($i, $currentRow, 'Кол-во чел.');
                else
                    $inputData->getSheet(0)->setCellValueByColumnAndRow($i, $currentRow, 'Кол-во ак. часов');
                $inputData->getSheet(0)->getStyleByColumnAndRow($i, $currentRow)->applyFromArray($styleArray);
            }

            $inputData->getSheet(0)->setCellValueByColumnAndRow(25, $currentRow, 'ИТОГО');
            $inputData->getSheet(0)->getStyleByColumnAndRow(25, $currentRow)->applyFromArray($styleArray);
                

            $inputData->getSheet(0)->getStyle('A'.($currentRow - 2).':Z'.$currentRow)->getFont()->setBold(true);
            $inputData->getSheet(0)->getRowDimension($currentRow)->setRowHeight(40);
            $inputData->getSheet(0)->getStyle('A'.$currentRow.':Z'.$currentRow)->getAlignment()->setWrapText(true);
            $inputData->getSheet(0)->getStyle('A'.($currentRow - 2).':Z'.$currentRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $inputData->getSheet(0)->getStyle('A'.($currentRow - 2).':Z'.$currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            

            $currentRow++; //пропускаем шапку таблицы


            $tgs = TeacherGroupWork::find()->where(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->andWhere(['IN', 'training_group_id', ExcelWizard::GetGroupsByBranchAndFocus($branch, 0, [0, 1])])
            ->andWhere(['teacher_id' => $teacher->id])
            ->all();

            $gIds = [];
            foreach ($tgs as $tg) $gIds[] = $tg->training_group_id;


            $tgs = TrainingGroupWork::find()->where(['IN', 'id', $gIds])->all();


            //заполняем таблицу с группами
            foreach ($tgs as $tg)
            {
                $currentColumn = 0;
                $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, $tg->number);
                $currentColumn++;
                $currentMonth = 1;
                $tempMonthCounter = 0;

                $parts = ExcelWizard::GetParticipantsByMonth($tg->id, $year);

                $diff = 0;
                if (explode("-", $tg->start_date)[0] < $year) //если группа начала занятия в прошлом году, накручиваем счетчик до текущего года
                    $diff = 13 - explode("-", $tg->start_date)[1] * 1;

                $tempMonthCounter += $diff;

                for ($currentMonth; $currentMonth < 13; $currentMonth++)
                {
                    $strMonth = $currentMonth > 9 ? $currentMonth : '0'.$currentMonth;
                    $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $tg->id])->andWhere(['>=', 'lesson_date', $year.'-'.$strMonth.'-01'])->andWhere(['<=', 'lesson_date', $year % 4 == 0 && $currentMonth == 2 ? $year.'-'.$strMonth.'-'.'29' : $year.'-'.$strMonth.'-'.$dayMonth[$currentMonth - 1]])->all();

                    $lIds = [];
                    foreach ($lessons as $lesson) $lIds[] = $lesson->id;

                    $lessonTeacher = LessonThemeWork::find()->where(['IN', 'training_group_lesson_id', $lIds])->andWhere(['teacher_id' => $teacher->id])->all();

                    $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, count($lessonTeacher));
                    $currentColumn++;


                    if ($tempMonthCounter < count($parts) && ($strMonth >= explode("-", $tg->start_date)[1] || $diff > 0))
                    {
                        $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, $parts[$tempMonthCounter]);
                        $tempMonthCounter++;
                    }
                    else
                    {
                        $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, 0);
                    }
                    $currentColumn++;

                }
                
                $temp = 0;
                //$str = '=СУММ(B'.$t.'*C'.$t.';D'.$t.'*E'.$t.';F'.$t.'*G'.$t.';H'.$t.'*I'.$t.';J'.$t.'*K'.$t.';L'.$t.'*M'.$t.';N'.$t.'*O'.$t.';P'.$t.'*Q'.$t.';R'.$t.'*S'.$t.';T'.$t.'*U'.$t.';V'.$t.'*W'.$t.';X'.$t.'*Y'.$t.')';
                
                for ($i = 1; $i < 24; $i += 2)
                {
                    $temp += $inputData->getSheet(0)->getCellByColumnAndRow($i, $currentRow)->getValue() * $inputData->getSheet(0)->getCellByColumnAndRow($i + 1, $currentRow)->getValue();
                }
                    //$temp += $inputData->getSheet(0)->getCellByColumnAndRow($i, $currentRow) * $inputData->getSheet(0)->getCellByColumnAndRow($i + 1, $currentRow);

                $inputData->getSheet(0)->setCellValueByColumnAndRow($currentColumn, $currentRow, $temp);
                $currentRow++;
            }

            // добавили в конце таблицы с группами итоговые по столбцам
            $inputData->getSheet(0)->setCellValueByColumnAndRow(0, $currentRow, 'ИТОГО');
            $inputData->getSheet(0)->getStyleByColumnAndRow(0, $currentRow)->applyFromArray($styleArray);
            for ($tempCurrentColumn = 1; $tempCurrentColumn < 26; $tempCurrentColumn++)
            {
                $temp = 0;
                for ($i = 0; $i < count($tgs); $i++)
                {
                    $temp += $inputData->getSheet(0)->getCellByColumnAndRow($tempCurrentColumn, $tempCurrentRow + $i)->getValue();
                }
                $inputData->getSheet(0)->setCellValueByColumnAndRow($tempCurrentColumn, $currentRow, $temp);
                $inputData->getSheet(0)->getStyleByColumnAndRow($tempCurrentColumn, $currentRow)->applyFromArray($styleArray);
            }

            $tempCurrentRow += 5 + count($tgs);
            $currentRow += 2;
        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        //$writer->setPreCalculateFormulas(true);
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
        while ($startRow < 100 && strlen($tempValue) < 1)
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

        $emailColumnIndex = 0;
        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($emailColumnIndex, $startRow)->getValue();
        while ($birthdateColumnIndex < 100 && $tempValue !== 'Контакт: Рабочий e-mail')
        {
            $emailColumnIndex++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($emailColumnIndex, $startRow)->getValue();
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

        $emails = [];
        $curEmail = "_";
        $startIndex = $startRow + 1;
        $mainIndex = 0;
        while ($mainIndex < $inputData->getActiveSheet()->getHighestRow() - $startRow)
        {
            $curEmail = $inputData->getActiveSheet()->getCellByColumnAndRow($emailColumnIndex, $startIndex + $mainIndex)->getFormattedValue();
            $emails[] = $curEmail;
            $mainIndex++;
        }
        //unset($birthdates[count($birthdates) - 1]);
        //unset($names[count($names) - 1]);

        $participants = array();
        for ($i = 0; $i != count($names); $i++)
        {
            $names[$i] = str_replace("\xC2\xA0", ' ', $names[$i]);
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

                if ($newParticipant->email == null && preg_match('/\S+@\S+\.\S+/', $emails[$i]))
                    $newParticipant->email = $emails[$i];

                $newParticipant->save();
            }
            $participants[] = $newParticipant;
        }
        return $participants;
    }

    static public function Enrolment ($order_id)
    {
        ini_set('memory_limit', '512M');

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/order_Enrolment.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/order_Enrolment.xlsx');

        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();
        $pastaAlDente = OrderGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find();
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = ResponsibleWork::find()->where(['document_order_id' => $order->id])->all();

        $c = 31;

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 8, $order->order_date);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 8, $order->order_number . '/' . $order->order_copy_id . '/' .  $order->order_postfix);
        $text = '';
        foreach ($groups as $group)
        {
            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $text .= $teacherTrG->teacherWork->shortName . ', ';
        }
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 15, '2. Назначить ' . $text . 'руководителем учебной группы, указанной в Приложении к настоящему приказу.');
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 16, '3. ' . $text . 'обеспечить:');
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 26, mb_substr($order->bring->firstname, 0, 1).'. '.mb_substr($order->bring->patronymic, 0, 1).'. '.$order->bring->secondname);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 27, mb_substr($order->executor->firstname, 0, 1).'. '.mb_substr($order->executor->patronymic, 0, 1).'. '.$order->executor->secondname);
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = mb_substr($res[$i]->people->firstname, 0, 1) .'. '. mb_substr($res[$i]->people->patronymic, 0, 1) .'. '. $res[$i]->people->secondname;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $c, '«____» ________ 20__ г.');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, $c, $fio);
        }
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, 77, 'от ' . $order->order_date . ' № ' . $order->order_number . '/' . $order->order_copy_id . '/' .  $order->order_postfix);
        $c = 80;

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $c, 'Учебная группа: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $trGroup->number);
            $c++;
            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Руководитель: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $teacherTrG->teacherWork->shortName);
            $c++;
            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Дополнительная общеразвивающая программа: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $programTrG->name);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Направленность: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $programTrG->stringFocus);
            $c++;
            $out = '';
            if ($programTrG->allow_remote == 0) $out = 'Только очная форма';
            if ($programTrG->allow_remote == 1) $out = 'Очная форма, с применением дистанционных технологий';
            if ($programTrG->allow_remote == 2) $out = 'Только дистанционная форма';
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Форма обучения: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $out);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Срок освоения: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'c ' . $trGroup->start_date . ' до ' . $trGroup->finish_date);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Дата зачисления: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $order->order_date);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Обучающиеся: ');
            $pasta = $pastaAlDente->where(['order_group_id' => $group->id])->all();
            foreach ($pasta as $macaroni)
            {
                $groupParticipant = $gPart->where(['id' => $macaroni->group_participant_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $participant->getFullName());
                $c++;
            }
            $c = $c + 2;
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=order_Enrolment.xls");
        header("Content-Transfer-Encoding: binary ");
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel5');
        $writer->save('php://output');
        exit;
    }

    static public function DownloadJournalAndKUG($training_group_id) {
        $onPage = 21; //количество занятий на одной строке в листе
        $lesCount = 0; //счетчик для страниц
        ini_set('memory_limit', '512M');

        $model = new JournalModel($training_group_id);

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

        $group = TrainingGroupWork::find()->where(['id' => $training_group_id])->one();
        $parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
        $lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();

        $flag = 1; // флаг вида журнала, в зависимости от количества детей
        if (count($parts) > 20)
        {
            $fileName = '/templates/electronicJournal2.xlsx';
            $flag = 0;
        }
        else
            $fileName = '/templates/electronicJournal.xlsx';

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath . $fileName);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath . $fileName);

        for ($i = 1; $i < count($lessons) / ($onPage * (1 + $flag)); $i++)
        {
            $clone = clone $inputData->getActiveSheet();
            $clone->setTitle('Шаблон' . $i);
            $inputData->addSheet($clone);
        }

        $magic = 0; //  смещение между страницами засчет фио+подписи и пустых строк
        $sheets = 0;
        while ($lesCount < count($lessons) / $onPage)
        {
            if ($lesCount !== 0 && $lesCount % 2 === 0)
            {
                $sheets++;
                $magic = 0;
            }
            if ($lesCount % 2 !== 0)
            {
                if ($flag == 1)
                    $magic = 25;
                else
                {
                    $sheets++;
                    $magic = 0;
                }
            }

            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(0, 1 + $magic, 'Группа: ' . $group->number);
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, 1 + $magic, 'Программа: ' . $group->programNameNoLink);
            $inputData->getSheet($sheets)->getStyle('B'. $magic)->getAlignment()->setWrapText(true)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            if ($magic === 25) $magic++;
            for ($i = 0; $i + $lesCount * $onPage < count($lessons) && $i < $onPage; $i++) //цикл заполнения дат на странице
            {
                $inputData->getSheet($sheets)->getCellByColumnAndRow(1 + $i, 4 + $magic)->setValueExplicit(date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                $inputData->getSheet($sheets)->getCellByColumnAndRow(1 + $i, 4 + $magic)->getStyle()->getAlignment()->setTextRotation(90);
            }

            for($i = 0; $i < count($parts); $i++) //цикл заполнения детей на странице
            {
                $inputData->getSheet($sheets)->setCellValueByColumnAndRow(0, $i + 6 + $magic, $parts[$i]->participantWork->shortName);
            }

            $lesCount++;
        }

        $delay = 0;
        for ($cp = 0; $cp < count($parts); $cp++)
        {
            $sheets = 0;
            $magic = 0;
            for ($i = 0; $i < count($lessons); $i++, $delay++)
            {
                $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$delay]])->one();

                if ($i % $onPage === 0 && $i !== 0)
                {
                    if (($magic === 26 && $flag === 1) || $flag === 0)
                    {
                        $magic = 0;
                        $sheets++;
                    }
                    else if ($flag === 1)
                        $magic = 26;
                }
                
                $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1 + $i % $onPage, 6 + $cp + $magic, $visits->excelStatus);
            }
        }

        for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
        {
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(31, 51, count($lessons)*count($parts));
        }

        $lessons = LessonThemeWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $training_group_id])
            ->orderBy(['trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.lesson_start_time' => SORT_ASC])->all();

        $magic = 5;
        $sheets = 0;
        foreach ($lessons as $lesson)
        {
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(25, $magic, date("d.m.y", strtotime($lesson->trainingGroupLesson->lesson_date)));
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(26, $magic, $lesson->theme);
            $magic++;

            if ($magic > 20 * (1 + $flag) + 5 + $flag)
            {
                $sheets++;
                if ($sheets > $inputData->getSheetCount())
                    break;
                $magic = 5;
            }
        }

        $themes = GroupProjectThemesWork::find()->where(['confirm' => 1])->andWhere(['training_group_id' => $training_group_id])->all();
        //var_dump($themes);
        $strThemes = 'Тема проекта: ';
        foreach ($themes as $theme)
            $strThemes .= $theme->projectTheme->name.', ';

        $strThemes = substr($strThemes, 0, -2);
        

        $orders = DocumentOrderWork::find()->joinWith(['orderGroups orderGroups'])->where(['orderGroups.training_group_id' => $training_group_id])->orderBy(['order_date' => SORT_ASC])->all();
        for ($i = 0, $magic = 25; $i < count($orders); )
        {
            if ($orders[$i]->order_postfix == null)
                for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
                {
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow($magic,51, $orders[$i]->order_number.'/'.$orders[$i]->order_copy_id);
                    
                }
            else
                for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
                {
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow($magic, 51, $orders[$i]->order_number.'/'.$orders[$i]->order_copy_id.'/'.$orders[$i]->order_postfix);
                }

            

            if ($i == count($orders) - 1)
                break;
            else
                $i = count($orders) - 1;
            $magic = 29;
        }

        for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow($magic,1, $strThemes);


        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=journal.xls");
        header("Content-Transfer-Encoding: binary ");
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel5');
        $writer->save('php://output');
    }
}