<?php

namespace app\models\work;

use app\models\common\ProgramErrors;
use Yii;


class ProgramErrorsWork extends ProgramErrors
{
    public function CheckErrorsTrainingProgram ($modelProgramID)
    {
        $oldErrors = ProgramErrorsWork::find()->where(['training_program_id' => $modelProgramID, 'time_the_end' => null])->all();

        $program = TrainingProgramWork::find()->where(['id' => $modelProgramID])->one();
        $tp = ThematicPlanWork::find()->where(['training_program_id' => $modelProgramID])->all();
        $tpCount = count($tp);
        $controle = 0;
        $authorsCount = count(AuthorProgramWork::find()->where(['training_program_id' => $modelProgramID])->all());

        foreach ($tp as $plane)
        {
            if ($plane->control_type_id === null)
                $controle++;
        }

        $checkList = ['tematicPlane' => 0, 'capacity' => 0, 'controle' => 0, 'thematicDirection' => 0, 'authors' => 0];

        foreach ($oldErrors as $correctErrors)
        {
            if ($correctErrors->errors_id == 7)
            {
                $checkList['tematicPlane'] = 1;
                if ($tpCount > 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 10)
            {
                $checkList['thematicDirection'] = 1;
                if ($program->thematic_direction_id !== null)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 11)
            {
                $checkList['controle'] = 1;
                if ($controle == 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 12)
            {
                $checkList['capacity'] = 1;
                if ($tpCount == $program->capacity)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 13)
            {
                $checkList['authors'] = 1;
                if ($authorsCount > 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            $correctErrors->save();
        }

        if ($checkList['tematicPlane'] == 0 && $tpCount == 0) // не заполнено утп
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 7;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['capacity'] == 0 && $tpCount !== $program->capacity)
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 12;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['controle'] == 0 && $controle > 0)
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 11;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['thematicDirection'] == 0 && $program->thematic_direction_id == NULL)
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 10;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['authors'] == 0 && $authorsCount == 0)
        {
            $this->training_program_id = $modelProgramID;
            $this->errors_id = 13;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }
}
