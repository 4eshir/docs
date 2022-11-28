<?php

namespace app\models\work;

use app\models\common\ProgramErrors;
use Yii;


class ProgramErrorsWork extends ProgramErrors
{
    public function ProgramAmnesty ($modelProgramID)
    {
        $errors = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelProgramID)
    {
        $errors = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    private function CheckThematicPlane ($modelProgramID, $tp)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 7])->all();
        $tpCount = count($tp);

        foreach ($err as $oneErr)
        {
            if ($tpCount > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $tpCount == 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 7;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckCapacity ($modelProgramID, $program, $tp)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 12])->all();
        $tpCount = count($tp);

        foreach ($err as $oneErr)
        {
            if ($tpCount === $program->capacity)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $tpCount !== $program->capacity) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 12;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckControl ($modelProgramID, $tp)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 11])->all();
        $controle = 0;
        foreach ($tp as $plane) {
            if ($plane->control_type_id === null)
                $controle++;
        }

        foreach ($err as $oneErr)
        {
            if ($controle == 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $controle > 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 11;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckThematicDirection ($modelProgramID, $program)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 10])->all();

        foreach ($err as $oneErr)
        {
            if ($program->thematic_direction_id !== null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $program->thematic_direction_id === NULL) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 10;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckAuthors ($modelProgramID)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 13])->all();
        $authorsCount = count(AuthorProgramWork::find()->where(['training_program_id' => $modelProgramID])->all());

        foreach ($err as $oneErr)
        {
            if ($authorsCount > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $authorsCount == 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 13;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckBranch ($modelProgramID)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 28])->all();
        $branchsCount = count(BranchProgramWork::find()->where(['training_program_id' => $modelProgramID])->all());

        foreach ($err as $oneErr)
        {
            if ($branchsCount > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $branchsCount == 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 28;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckDatePedCouncil ($modelProgramID, $program)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 29])->all();

        foreach ($err as $oneErr)
        {
            if ($program->ped_council_date !== NULL)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $program->ped_council_date === NULL) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 29;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckDocs($modelProgramID, $program)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 44])->all();

        foreach ($err as $oneErr)
        {
            if ($program->doc_file != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $program->doc_file == null)
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 44;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckEditDocs($modelProgramID, $program)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 45])->all();

        foreach ($err as $oneErr)
        {
            if ($program->edit_docs != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $program->edit_docs == null)
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 45;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsTrainingProgram($modelProgramID)
    {
        $program = TrainingProgramWork::find()->where(['id' => $modelProgramID])->one();
        $tp = ThematicPlanWork::find()->where(['training_program_id' => $modelProgramID])->all();

        $this->CheckThematicPlane($modelProgramID, $tp);
        $this->CheckCapacity($modelProgramID, $program, $tp);
        $this->CheckControl($modelProgramID, $tp);
        $this->CheckThematicDirection($modelProgramID, $program);
        $this->CheckAuthors($modelProgramID);
        $this->CheckBranch($modelProgramID);
        $this->CheckDatePedCouncil($modelProgramID, $program);
        $this->CheckDocs($modelProgramID, $program);
        $this->CheckEditDocs($modelProgramID, $program);
    }

    public function CheckErrorsTrainingProgramWithoutAmnesty($modelProgramID)
    {
        $this->NoAmnesty($modelProgramID);
        $this->CheckErrorsTrainingProgram($modelProgramID);
    }
}
