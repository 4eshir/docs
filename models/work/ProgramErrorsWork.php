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

    public function CheckThematicPlane ($modelProgramID, $tp)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 7])->all();
        $amnesty = 0;
        $tpCount = count($tp);

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
            {
                if ($tpCount > 0)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $tpCount == 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 7;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckCapacity ($modelProgramID, $program, $tp)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 12])->all();
        $amnesty = 0;
        $tpCount = count($tp);

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
            {
                if ($tpCount === $program->capacity)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $tpCount !== $program->capacity) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 12;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckControl ($modelProgramID, $tp)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 11])->all();
        $amnesty = 0;
        $controle = 0;
        foreach ($tp as $plane) {
            if ($plane->control_type_id === null)
                $controle++;
        }

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
            {
                if ($controle == 0)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    var_dump($oneErr);
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $controle > 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 11;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckThematicDirection ($modelProgramID, $program)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 10])->all();
        $amnesty = 0;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
            {
                if ($program->thematic_direction_id !== null)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $program->thematic_direction_id === NULL) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 10;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckAuthors ($modelProgramID)
    {
        $err = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null, 'errors_id' => 13])->all();
        $amnesty = 0;
        $authorsCount = count(AuthorProgramWork::find()->where(['training_program_id' => $modelProgramID])->all());

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
            {
                if ($authorsCount > 0)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $authorsCount == 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 13;
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
    }
}
