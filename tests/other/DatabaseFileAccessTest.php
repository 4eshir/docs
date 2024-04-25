<?php

namespace tests\other;

use app\models\common\TrainingProgram;
use app\models\components\YandexDiskContext;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\BackupVisitWork;
use app\models\work\DocumentInWork;
use app\models\work\DocumentOrderWork;
use app\models\work\DocumentOutWork;
use app\models\work\EventWork;
use app\models\work\ForeignEventWork;
use app\models\work\InvoiceWork;
use app\models\work\ParticipantFilesWork;
use app\models\work\RegulationWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\VisitWork;
use tests\other\models\FileAccessTest\FileAccessModel;
use tests\other\models\FileAccessTest\TableColumnNames;
use Yii;

class DatabaseFileAccessTest
{
    private $tableColumns = [];

    /*
     * Класс, реализующий бизнес-логику
     * для проверки доступности файлов из БД
     * с сервера или Яндекс.Диска
     */


    //--Описание всех таблиц, содержащих ссылки на файлы--
    function __construct()
    {
        $documentIn = new TableColumnNames(DocumentInWork::find(), ['scan', 'doc', 'applications'], ['upload/files/document-in/scan', 'upload/files/document-in/docs', 'upload/files/document-in/apps']);
        $documentOut = new TableColumnNames(DocumentOutWork::find(), ['Scan', 'doc', 'applications'], ['upload/files/document-out/scan', 'upload/files/document-out/docs', 'upload/files/document-out/apps']);
        $documentOrder = new TableColumnNames(DocumentOrderWork::find(), ['scan', 'doc'], ['upload/files/document-order/scan', 'upload/files/document-order/docs']);
        $event = new TableColumnNames(EventWork::find(), ['protocol', 'photos', 'reporting_doc', 'other_files'], ['upload/files/event/protocol', 'upload/files/event/photos', 'upload/files/event/reporting', 'upload/files/event/other']);
        $foreignEvent = new TableColumnNames(ForeignEventWork::find(), ['docs_achievement'], ['upload/files/foreign-event/docs_achievement']);
        $invoice = new TableColumnNames(InvoiceWork::find(), ['document'], ['upload/files/invoice/document']);
        $participantsFile = new TableColumnNames(ParticipantFilesWork::find(), ['filename'], ['upload/files/foreign-event/participants']);
        $regulation = new TableColumnNames(RegulationWork::find(), ['scan'], ['upload/files/regulation']);
        $trainingGroup = new TableColumnNames(TrainingGroupWork::find(), ['photos', 'present_data', 'work_data'], ['upload/files/training-group/photos', 'upload/files/training-group/present_data', 'upload/files/training-group/work_data']);
        $trainingProgram = new TableColumnNames(TrainingProgramWork::find(), ['doc_file', 'edit_docs', 'contract'], ['upload/files/training-program/doc', 'upload/files/training-program/edit_docs', 'upload/files/training-program/contract']);

        $this->tableColumns = [$documentIn, $documentOut, $documentOrder, $event, $foreignEvent, $invoice, $participantsFile, $regulation, $trainingGroup, $trainingProgram];
    }


    //--Основной метод проверки всех файлов на доступность--
    // part - частичная проверка по массиву tableColumns.
    // - 'all' - по всему массиву
    // - [0, 1...] - часть массива по индексам
    public function GetFileAccess($part = 'all')
    {
        $newTableColumns = [];
        if ($part !== 'all')
            foreach ($part as $one)
                $newTableColumns[] = $this->tableColumns[$one];
        else
            $newTableColumns = $this->tableColumns;


        $fileAccesses = [];

        $allTables = [];
        foreach ($newTableColumns as $tableColumn)
            $allTables[] = $tableColumn->tableName->all();

        for ($j = 0; $j < count($newTableColumns); $j++)
        {
            $rows = $allTables[$j];
            foreach ($rows as $row)
            {
                for ($i = 0; $i < count($newTableColumns[$j]->fileColumns); $i++)
                {
                    if ($row[$newTableColumns[$j]->fileColumns[$i]] !== null && strlen($row[$newTableColumns[$j]->fileColumns[$i]]) > 1)
                    {

                        $files = $this->SplitFilenames($row[$newTableColumns[$j]->fileColumns[$i]]);
                        foreach ($files as $file)
                        {
                            if (strlen($file) > 1)
                            {
                                $oneFile = new FileAccessModel();
                                $oneFile->filepath = '/' .$newTableColumns[$j]->pathes[$i].'/'.$file;
                                $this->CheckFileAvailable($oneFile);
                                $fileAccesses[] = $oneFile;
                            }

                        }

                    }

                }
            }
        }

        return $fileAccesses;
    }

    //--Проверка одного файла на доступность по пути, строке таблицы и названию поля в таблице--
    private function CheckFileAvailable($file)
    {

        // Проверка нахождения файла на сервере
        if (file_exists(Yii::$app->basePath.$file->filepath))
        {
            $file->access = true;
            $file->repoType = FileAccessModel::SERV;
        }
        // Проверка нахождения файла на Яндекс.Диске
        else if (YandexDiskContext::CheckSameFile(FileDownloadYandexDisk::ADDITIONAL_PATH.$file->filepath))
        {
            $file->access = true;
            $file->repoType = FileAccessModel::YADI;
        }
        // Файл не найден
        else
            $file->access = false;

    }

    //--Разделение строки на несколько имен файлов--
    private function SplitFilenames($filenames)
    {
        $split = explode(" ", $filenames);
        return $split;
    }


    //--Временная функция для проверки целостности Visit в группах--
    public function CheckVisitIntegrity()
    {
        $groupNames = [];
        $groupStatus = [];

        $groups = TrainingGroupWork::find()->orderBy(['id' => SORT_DESC])->all();

        foreach ($groups as $group) {
            $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->id])->all();
            $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
            $numb1 = count($lessons) * count($parts);

            $lIds = [];
            foreach ($lessons as $lesson) $lIds[] = $lesson->id;

            $visits = VisitWork::find()->where(['IN', 'training_group_lesson_id', $lIds])->all();
            $numb2 = count($visits);

            $groupNames[] = $group->number;
            if ($numb1 !== $numb2) $groupStatus[] = 0;
            else $groupStatus[] = 1;
        }

        return [$groupNames, $groupStatus];

    }
    public function CheckVisitSame()
    {
        $errorVisits = [];
        $visitsAll = BackupVisitWork::find()->orderBy(['foreign_event_participant_id' => SORT_DESC, 'training_group_lesson_id' => SORT_DESC])->all();

        for ($i = 0; $i < count($visitsAll) - 1; $i++)
        {
            if ($visitsAll[$i]->foreign_event_participant_id == $visitsAll[$i + 1]->foreign_event_participant_id
                && $visitsAll[$i]->training_group_lesson_id == $visitsAll[$i + 1]->training_group_lesson_id)
                $errorVisits[] = $visitsAll[$i];
        }

        return $errorVisits;
    }

}