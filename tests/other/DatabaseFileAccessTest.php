<?php

namespace tests\other;

use app\models\common\TrainingProgram;
use app\models\work\DocumentInWork;
use app\models\work\DocumentOrderWork;
use app\models\work\DocumentOutWork;
use app\models\work\EventWork;
use app\models\work\ForeignEventWork;
use app\models\work\InvoiceWork;
use app\models\work\ParticipantFilesWork;
use app\models\work\RegulationWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use tests\other\models\FileAccessTest\FileAccessModel;
use tests\other\models\FileAccessTest\TableColumnNames;

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
        $documentIn = new TableColumnNames(DocumentInWork::find(), ['scan', 'doc', 'applications'], ['upload\files\document-in\scan', 'upload\files\document-in\docs', 'upload\files\document-in\apps']);
        $documentOut = new TableColumnNames(DocumentOutWork::find(), ['Scan', 'doc', 'applications'], ['upload\files\document-out\scan', 'upload\files\document-out\docs', 'upload\files\document-out\apps']);
        $documentOrder = new TableColumnNames(DocumentOrderWork::find(), ['scan', 'doc'], ['upload\files\document-order\scan', 'upload\files\document-order\docs']);
        $event = new TableColumnNames(EventWork::find(), ['protocol', 'photos', 'reporting_doc', 'other_files'], ['upload\files\event\protocol', 'upload\files\event\photos', 'upload\files\event\reporting', 'upload\files\event\other']);
        $foreignEvent = new TableColumnNames(ForeignEventWork::find(), ['docs_achievement'], ['upload\files\foreign-event\docs_achievement']);
        $invoice = new TableColumnNames(InvoiceWork::find(), ['document'], ['upload\files\invoice\document']);
        $participantsFile = new TableColumnNames(ParticipantFilesWork::find(), ['filename'], ['upload\files\foreign-event\participants']);
        $regulation = new TableColumnNames(RegulationWork::find(), ['scan'], ['upload\files\regulation']);
        $trainingGroup = new TableColumnNames(TrainingGroupWork::find(), ['photos', 'present_data', 'work_data'], ['upload\files\training-group\photos', 'upload\files\training-group\present_data', 'upload\files\training-group\work_data']);
        $trainingProgram = new TableColumnNames(TrainingProgramWork::find(), ['doc_file', 'edit_docs', 'contract'], ['upload\files\training-program\doc', 'upload\files\training-program\edit_docs', 'upload\files\training-program\contract']);

        $this->tableColumns = [$documentIn, $documentOut, $documentOrder, $event, $foreignEvent, $invoice, $participantsFile, $regulation, $trainingGroup, $trainingProgram];
    }


    //--Основной метод проверки всех файлов на доступность--
    public function GetFileAccess()
    {
        $fileAccesses = [];

        foreach ($this->tableColumns as $tableColumn)
        {
            $rows = $tableColumn->tableName->all();
            foreach ($rows as $row)
            {
                for ($i = 0; $i < count($tableColumn->fileColumns); $i++)
                {
                    if ($row[$tableColumn->fileColumns[$i]] !== null && strlen($row[$tableColumn->fileColumns[$i]]) > 1)
                    {
                        $oneFile = new FileAccessModel();
                        $oneFile->filepath = $tableColumn->pathes[$i].'\\'.$row[$tableColumn->fileColumns[$i]];
                        $oneFile->access = $this->CheckFileAvailable($oneFile);
                        $fileAccesses[] = $oneFile;
                    }

                }
            }
        }

        return $fileAccesses;
    }

    //--Проверка одного файла на доступность по пути, строке таблицы и названию поля в таблице--
    private function CheckFileAvailable($file)
    {

        if (file_exists($file->filepath))
            return true;
        else
            return false;
    }
}