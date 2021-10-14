<?php

namespace app\controllers;

use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\work\AuditoriumWork;
use app\models\DynamicModel;
use app\models\work\ProgramErrorsWork;
use app\models\work\TrainingProgramWork;
use Yii;
use app\models\work\BranchWork;
use app\models\SearchBranch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DaemonController extends Controller
{

    public function actionProgramErrors()
    {

        $programs = TrainingProgramWork::find()->all();
        foreach ($programs as $program)
        {
            $errorsProgramCheck = new ProgramErrorsWork();
            $errorsProgramCheck->CheckErrorsTrainingProgram($program->id);
        }

        Logger::WriteLog(1, $programs[0]->name);
    }

}
