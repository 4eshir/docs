<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\common\ForeignEventParticipants;
use app\models\common\Team;
use app\models\components\Logger;
use app\models\components\report\debug_models\DebugManHoursModel;
use app\models\components\report\ReportConst;
use app\models\components\report\SupportReportFunctions;
use app\models\LoginForm;
use app\models\null\PeopleNull;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\DocumentOrderWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\ParticipantFilesWork;
use app\models\work\PeopleWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamNameWork;
use app\models\work\TeamWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SupCommandsController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {

        $this->stdout($message."\n", Console::FG_RED);
        //echo '<color="green">'.$message.'</color>' . "\n";

        return ExitCode::OK;
    }


    // --Поиск расхождений в количестве участников и победителей мероприятий--
    // -- Формат: Мероприятие | ФИО | Кол-во фактов участия | Кол-во фактов побед/приз. --
    // -- Исходная таблица: foreign_event
    public function actionCheckEventDifference()
    {
        $events = ForeignEventWork::find()->all();

        foreach ($events as $event)
        {
            $allDistinctParticipants = TeacherParticipantWork::find()->select('participant_id')->distinct()->where(['foreign_event_id' => $event->id])->all();
            $pIds = [];
            foreach ($allDistinctParticipants as $one) $pIds[] = $one->participant_id;

            foreach ($pIds as $id)
            {
                $participant = ForeignEventParticipantsWork::find()->where(['id' => $id])->one();
                $partFacts = count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['participant_id' => $id])->all());
                $prizeFacts = count(ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['participant_id' => $id])->all());

                if ($partFacts < $prizeFacts)
                    $this->stdout($event->name.' | '.$participant->fullName.' | '.$partFacts. ' | '.$prizeFacts ."\n", Console::FG_RED);
            }
        }

        return ExitCode::OK;
    }

    public function actionCheckTime()
    {
        $count = 1000000;

        $start1 = microtime(true);
        for ($i = 0; $i < $count; $i++)
            $res = TrainingGroupParticipantWork::find()->where(['training_group_id' => $i])->orWhere(['participant_id' => $i])->all();

        $this->stdout('Time 1: '.round(microtime(true) - $start1, 2)."\n", Console::FG_PURPLE);

        $start2 = microtime(true);
        $res = TrainingGroupParticipantWork::find()->all();
        $tr = [];
        for ($i = 0; $i < $count; $i++)
            for ($j = 0; $j < count($res); $j++)
                if ($res[$j]->participant_id == $i && $res[$j]->training_group_id == $i)
                    $tr[] = $res[$j];

        $this->stdout('Time 2: '.round(microtime(true) - $start2, 2), Console::FG_GREEN);
    }

    public function actionCheckMemory($type)
    {
        $count = 100000;

        if ($type == 1)
        {
            $start1 = memory_get_usage();
            for ($i = 0; $i < $count; $i++)
                $res = TrainingGroupParticipantWork::find()->where(['training_group_id' => $i])->orWhere(['participant_id' => $i])->all();

            $this->stdout('Time 1: '.round(memory_get_usage() - $start1, 2)."\n", Console::FG_PURPLE);
        }
        else
        {
            $start2 = memory_get_usage();
            $res = TrainingGroupParticipantWork::find()->all();
            $tr = [];
            for ($i = 0; $i < $count; $i++)
                for ($j = 0; $j < count($res); $j++)
                    if ($res[$j]->participant_id == $i || $res[$j]->training_group_id == $i)
                        $tr[] = $res[$j];

            $this->stdout('Time 2: '.round(memory_get_usage() - $start2, 2), Console::FG_PURPLE);
        }

    }


    public function actionConvertToTeacherParticipant()
    {

        //--Конвертируем таблицу participant_achievement--

        $participantAchievements = ParticipantAchievementWork::find()->all();
        $usedTeacherParticipantIds = [];

        $errors = [];

        foreach ($participantAchievements as $one)
        {
            $teacherParticipant = TeacherParticipantWork::find()->where(['foreign_event_id' => $one->foreign_event_id])->andWhere(['participant_id' => $one->participant_id])->andWhere(['NOT IN', 'id', $usedTeacherParticipantIds])->one();

            if ($teacherParticipant !== null)
            {
                $usedTeacherParticipantIds[] = $teacherParticipant->id;
                $one->teacher_participant_id = $teacherParticipant->id;
                $one->save();
            }
            else
                $errors[] = $one->id;
        }

        $this->stdout("----Error achievements----\n", Console::FG_YELLOW);

        foreach ($errors as $error)
        {
            $pa = ParticipantAchievementWork::find()->where(['id' => $error])->one();
            if ($pa !== null) $this->stdout($pa->id." ".$pa->participantWork->fullName." ".$pa->foreign_event_id."\n", Console::FG_RED);
        }

        //------------------------------------------------

        $this->stdout("\n\n", Console::FG_YELLOW);

        //--Конвертируем таблицу team--

        $events = ForeignEventWork::find()->all();

        $teamErrors = [];

        foreach ($events as $event)
        {
            $oldTeams = TeamWork::find()->where(['foreign_event_id' => $event->id])->orderBy(['name' => SORT_ASC, 'participant_id' => SORT_ASC])->all();


            if (count($oldTeams) > 0)
            {
                $currentTeamName = $oldTeams[0]->name;

                $newTeam = new TeamNameWork();
                $newTeam->name = $currentTeamName;
                $newTeam->foreign_event_id = $event->id;
                $newTeam->save();

                $currentTeamNameId = $newTeam->id;

                foreach ($oldTeams as $oldTeam)
                {
                    if ($currentTeamName !== $oldTeam->name && $oldTeam->name !== null)
                    {
                        $currentTeamName = $oldTeam->name;

                        $newTeam = new TeamNameWork();
                        $newTeam->name = $currentTeamName;
                        $newTeam->foreign_event_id = $event->id;
                        $newTeam->save();

                        $currentTeamNameId = $newTeam->id;
                    }

                    $teacherParticipant = TeacherParticipantWork::find()->where(['foreign_event_id' => $oldTeam->foreign_event_id])
                        ->andWhere(['participant_id' => $oldTeam->participant_id])->one();

                    if ($teacherParticipant !== null)
                    {
                        $oldTeam->teacher_participant_id = $teacherParticipant->id;
                        $oldTeam->team_name_id = $currentTeamNameId;
                        $oldTeam->save();
                    }
                    else
                        $teamErrors[] = $oldTeam->id;


                }
            }

        }

        $this->stdout("----Error teams----\n", Console::FG_YELLOW);

        foreach ($teamErrors as $error)
        {
            $pa = TeamWork::find()->where(['id' => $error])->one();
            if ($pa !== null) $this->stdout($pa->id." ".$pa->name." ".$pa->foreign_event_id."\n", Console::FG_RED);
        }

        //-----------------------------

        //--Конвертируем таблицу participant_files--

        $participantFiles = ParticipantFilesWork::find()->all();
        $usedTeacherParticipantIds = [];

        $errors = [];

        foreach ($participantFiles as $one)
        {
            $teacherParticipant = TeacherParticipantWork::find()->where(['foreign_event_id' => $one->foreign_event_id])->andWhere(['participant_id' => $one->participant_id])->andWhere(['NOT IN', 'id', $usedTeacherParticipantIds])->one();

            if ($teacherParticipant !== null)
            {
                $usedTeacherParticipantIds[] = $teacherParticipant->id;
                $one->teacher_participant_id = $teacherParticipant->id;
                $one->save();
            }
            else
                $errors[] = $one->id;
        }

        $this->stdout("----Error files----\n", Console::FG_YELLOW);

        foreach ($errors as $error)
        {
            $pa = ParticipantFilesWork::find()->where(['id' => $error])->one();
            if ($pa !== null) $this->stdout($pa->id." ".$pa->participant->secondname." ".$pa->foreign_event_id."\n", Console::FG_RED);
        }

        //------------------------------------------



    }


    public function actionWriteGroups()
    {
        $main_sum = 0;

        $start_date = '2023-01-01';
        $end_date = '2023-09-12';

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::CDNTT], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        foreach ($targetGroups as $group)
        {
            $allCdnttTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, [$group], 0, ReportConst::AGES_ALL, $end_date);
            $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttTechnical, $start_date, $end_date, VisitWork::PRESENCE_AND_ABSENCE);

            $this->stdout($group->number." ".count($visits)."\n", Console::FG_CYAN);
            $main_sum += count($visits);
        }
        $this->stdout("\n");


        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::CDNTT], [FocusWork::ART], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        foreach ($targetGroups as $group)
        {
            $allCdnttArt = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, [$group], 0, ReportConst::AGES_ALL, $end_date);
            $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttArt, $start_date, $end_date, VisitWork::PRESENCE_AND_ABSENCE);

            $this->stdout($group->number." ".count($visits)."\n", Console::FG_GREEN);
            $main_sum += count($visits);
        }
        $this->stdout("\n");


        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::CDNTT], [FocusWork::SOCIAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        foreach ($targetGroups as $group)
        {
            $allCdnttSocial = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, [$group], 0, ReportConst::AGES_ALL, $end_date);
            $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttSocial, $start_date, $end_date, VisitWork::PRESENCE_AND_ABSENCE);

            $this->stdout($group->number." ".count($visits)."\n", Console::FG_YELLOW);
            $main_sum += count($visits);
        }
        $this->stdout("\nВсего посещений: ".$main_sum);
    }


    /*--УДАЛИТЬ--
    public function actionTemp()
    {
        // шаблон ошибки, выводимой в файл
        $error_template = 'mysqldump: Got error:';

        // путь сохранения файла бэкапа
        $filepath = Yii::$app->basePath.'/../db_backups/'.date('Ymd-his').'__db_dskd.sql';

        // конфигурации БД
        $db_config = include Yii::$app->basePath.'/config/db.php';

        $username = $db_config["username"].'r';
        $password = $db_config["password"];
        $host = explode('=', explode(':', explode(';', $db_config["dsn"])[0])[1])[1];
        $db_name = explode('=', explode(';', $db_config["dsn"])[1])[1];

        // функция записи бэкапа в файл, с перенаправлением STDERR>STDOUT
        exec('mysqldump --user=' . $username . ' --password=' . $password . ' --host=' . $host .
            ' ' . $db_name . '> ' . $filepath . ' 2>&1');

        // получаем данные из файла
        $filedata = fopen($filepath, 'r') or die("Cannot find file!");
        $file_first_str = htmlentities(fgets($filedata));

        // если в файле информация об ошибке - удаляем файл
        if (var_export(stripos($file_first_str, $error_template), true) !== 'false')
            unlink($filepath);
    }
    //--УДАЛИТЬ--*/

    public function actionTest()
    {
        //$this->stdout("\nBOOBS\n", Console::FG_CYAN);
        $fileName = 'Док.20220812_Основы_микробиологии_и_биотехнологии.docx';
        $type = 'doc';
        //$path = \Yii::getAlias('@upload') ;

        $filePath = '/upload/files/training-program';
        $filePath .= $type == null ? '/' : '/'.$type.'/';

        $downloadServ = new FileDownloadServer($filePath, $fileName);
        $this->stdout('$downloadServ OK', Console::FG_RED);
        $downloadYadi = new FileDownloadYandexDisk($filePath, $fileName);
        $this->stdout('downloadYadi OK', Console::FG_RED);

        $downloadServ->LoadFile();
        $this->stdout($downloadServ->success ? 'true' : 'false', Console::FG_CYAN);
        if (!$downloadServ->success) $downloadYadi->LoadFile();
        else return \Yii::$app->response->sendFile($downloadServ->file);
        $this->stdout($downloadYadi->success ? 'true' : 'false', Console::FG_YELLOW);
        /*if (!$downloadYadi->success) throw new \Exception('File not found');
        else {

            $fp = fopen('php://output', 'r');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);
        }*/
    }

    public function actionTemp()
    {
        $groups3 = SupportReportFunctions::GetTrainingGroups(
            ReportConst::PROD,
            '2023-01-01', '2023-09-29',
            [BranchWork::TECHNO],
            [FocusWork::TECHNICAL],
            [AllowRemoteWork::FULLTIME],
            [ReportConst::BUDGET],
            [],
            [ReportConst::START_IN_END_IN]);

        $groups3Id = SupportReportFunctions::GetIdFromArray($groups3);

        $this->stdout(TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups3Id])->createCommand()->getRawSql());


    }

    private function scan($dir, $backup_dir_name)
    {
        $dirCanonical = realpath($dir);
        if ($fileOrDir = opendir($dirCanonical))
        {
            while ($fileName = readdir($fileOrDir))
            {
                $this->createDirAndFile($backup_dir_name, $dirCanonical, !is_dir($dirCanonical.DIRECTORY_SEPARATOR.$fileName) ? $fileName : null);

                if($fileName == "." || $fileName == "..")
                    continue;

                $callBack=$dirCanonical.DIRECTORY_SEPARATOR.$fileName;
                //$this->stdout($callBack."\n");
                if (is_dir($callBack))
                {
                    $this->scan($callBack, $backup_dir_name);
                }
            }
        }
    }

    private function createDirAndFile($backup_dir_name, $dir, $fileName = null)
    {
        $real_dir = Yii::$app->basePath.'/../src_backups/'.$backup_dir_name.'__src_dskd/docs/'.str_replace($dir, Yii::$app->basePath, '');
        $this->stdout($real_dir.' '.$fileName."\n", Console::FG_YELLOW);
        //$this->stdout($dir.DIRECTORY_SEPARATOR.$fileName."\n", Console::FG_PURPLE);

        if(!is_dir($real_dir))
            mkdir($real_dir, 0777, true);

        if ($fileName !== null)
            copy($dir.DIRECTORY_SEPARATOR.$fileName, $real_dir.DIRECTORY_SEPARATOR.$fileName);
    }


}
