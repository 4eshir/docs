<?php

namespace app\models\work;

use app\models\common\AllowRemote;
use app\models\common\AuthorProgram;
use app\models\common\BranchProgram;
use app\models\common\Focus;
use app\models\common\People;
use app\models\common\ThematicDirection;
use app\models\common\ThematicPlan;
use app\models\common\TrainingProgram;
use app\models\components\ExcelWizard;
use app\models\components\FileWizard;
use app\models\null\FocusNull;
use app\models\null\PeopleNull;
use app\models\null\ThematicDirectionNull;
use Yii;
use yii\helpers\Html;
use app\models\components\Logger;


class TrainingProgramWork extends TrainingProgram
{
    public $isTechnopark;
    public $isQuantorium;
    public $isCDNTT;
    public $isMobQuant;
    public $isCod;

    public $authors;
    public $thematicPlan;

    public $docFile;
    public $editDocs;
    public $contractFile;

    public $fileUtp;

    public $archStat;

    public $linkGroups;

    public function rules()
    {
        return [
            [['name', 'hour_capacity', 'capacity'], 'required'],
            ['description', 'string'],
            [['ped_council_date', 'linkGroups'], 'safe'],
            [['student_left_age'], 'double'],
            [['focus_id', 'author_id', 'capacity', 'student_right_age', 'allow_remote_id', 'isCDNTT', 'isCod', 'isQuantorium', 'isTechnopark', 'isMobQuant', 'thematic_direction_id', 'level', 'hour_capacity', 'actual', 'archStat', 'certificat_type_id', 'is_network'], 'integer'],
            [['name', 'ped_council_number', 'doc_file', 'edit_docs', 'key_words'], 'string', 'max' => 1000],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['thematic_direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThematicDirection::className(), 'targetAttribute' => ['thematic_direction_id' => 'id']],
            [['docFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true],
            [['editDocs'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['fileUtp'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'nameX' => 'Название',
            'ped_council_date' => 'Дата педагогического совета',
            'ped_council_number' => 'Номер протокола педагогического совета',
            'author_id' => 'Составитель',
            'thematic_direction_id' => 'Тематическое направление',
            'level' => 'Уровень сложности',
            'authorsList' => 'Составители',
            'compilers' => 'Составители',
            'capacity' => 'Объем, ак. час.',
            'student_left_age' => 'Мин. возраст учащихся, лет',
            'student_right_age' => 'Макс. возраст учащихся, лет',
            'studentAge' => 'Возраст учащихся, лет',
            'focus_id' => 'Направленность',
            'stringFocus' => 'Направленность',
            'allowRemote' => 'Форма реализации',
            'allow_remote_id' => 'Форма реализации',
            'doc_file' => 'Документ программы',
            'docFile' => 'Документ программы',
            'edit_docs' => 'Редактируемые документы',
            'editDocs' => 'Редактируемые документы',
            'key_words' => 'Ключевые слова',
            'isCDNTT' => 'ЦДНТТ',
            'isQuantorium' => 'Кванториум',
            'isTechnopark' => 'Технопарк',
            'isCod' => 'Центр одаренных детей',
            'isMobQuant' => 'Мобильный кванториум',
            'branchs' => 'Отдел(-ы) - место реализации',
            'hour_capacity' => 'Длительность 1 академического часа в минутах',
            'actual' => 'Образовательная программа актуальна',
            'fileUtp' => 'Файл УТП',
            'actualExport' => 'Актуальность',
            'creatorString' => 'Регистратор программы',
            'lastUpdateString' => 'Последний редактор программы',
            'is_network' => 'Сетевая форма обучения',
            'isNetwork' => 'Сетевая форма обучения',
            'contract' => 'Договор о сетевой форме обучения',
            'contractFile' => 'Договор о сетевой форме обучения',
        ];
    }

    public function getActualExport()
    {
        return $this->actual == 0 ? 'Не актуальна' : 'Актуальна';
    }

    public function getIsNetwork()
    {
        return $this->is_network == 0 ? 'Нет' : 'Да';
    }

    public function getCertificatTypeString()
    {
        return $this->certificatType->name;
    }

    public function getCreatorString()
    {
        $res = 'Неизвестно';
        if ($this->creator_id !== null)
        {
            $user = UserWork::find()->where(['id' => $this->creator_id])->one();
            $res = Html::a($user->fullName, \yii\helpers\Url::to(['user/view', 'id' => $user->id]));
        }

        return $res;
    }

    public function getLastUpdateString()
    {
        $res = 'Неизвестно';
        if ($this->last_update_id !== null)
        {
            $user = UserWork::find()->where(['id' => $this->last_update_id])->one();
            $res = Html::a($user->fullName, \yii\helpers\Url::to(['user/view', 'id' => $user->id]));
        }

        return $res;
    }

    public function getNameX()
    {
        return $this->name.' ('.$this->id.')';
    }

    public function getLinkGroups()
    {
        $groups = TrainingGroupWork::find()->where(['training_program_id' => $this->id])->orderBy(['start_date' => SORT_DESC])->all();
        $res = '<table>';
        
        foreach ($groups as $group)
        {
            $style = "";
            $strStatus = '(группа завершила обучение)';
            if (date("Y-m-d") > $group->start_date && date("Y-m-d") < $group->finish_date)
            {
                $style .= "background: #77DD77";
                $strStatus = '(группа проходит обучение)';
            }
            if (date("Y-m-d") < $group->start_date)
            {
                $style .= "background: #FFBA00";
                $strStatus = '(группа не начала обучение)';
            }
            $res .= '<tr><td style="padding-right: 15px; padding-bottom: 2px">'.Html::a($group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id])).'</td><td style="padding-right: 15px">'.$group->start_date.'&mdash;'.$group->finish_date.'</td><td style="'.$style.'"><i><b>'.$strStatus.'<b></i></td>';
        }
        $res .= '</table>';
        return $res;
    }

    public function getGroupsCount()
    {
        return '<span>Всего групп: <b>'.count(TrainingGroupWork::find()->where(['training_program_id' => $this->id])->all()).'</b></span>';
    }

    public function getFullName()
    {
        $authors = AuthorProgramWork::find()->where(['training_program_id' => $this->id])->all();
        $result = '';
        foreach ($authors as $author)
        {
            $result .= $author->authorWork->shortName.', ';
        }
        $result = substr($result, 0, -2);
        $result .= ' Дата утверждения: ' .$this->ped_council_date;
        return $this->name.' ('.$result.')';
    }

    public function getCompilers()
    {
        $authors = AuthorProgramWork::find()->where(['training_program_id' => $this->id])->all();
        $result = '';
        foreach ($authors as $author)
        {
            $result .= Html::a($author->authorWork->shortName, \yii\helpers\Url::to(['people/view', 'id' => $author->author_id])).'<br>';
        }
        return $result;
    }

    public function getAuthorWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'author_id']);
        return $try->all() ? $try : new PeopleNull();
    }


    public function getFocusWork()
    {
        $try = $this->hasOne(FocusWork::className(), ['id' => 'focus_id']);
        return $try->all() ? $try : new FocusNull();
    }


    public function getThematicDirectionWork()
    {
        $try = $this->hasOne(ThematicDirectionWork::className(), ['id' => 'thematic_direction_id']);
        return $try->all() ? $try : new ThematicDirectionNull();
    }

    public function getAuthorsList()
    {
        $authors = AuthorProgram::find()->where(['training_program_id' => $this->id])->all();
        $result = '';
        foreach ($authors as $author)
        {
            $result .= $author->author->shortName.'<br>';
        }
        return $result;
    }

    public function getStudentAge()
    {
        return $this->student_left_age.' - '.$this->student_right_age.' л.';
    }

    public function getAllowRemote()
    {
        //return $this->allow_remote == 0 ? 'Нет' : 'Да';
        return AllowRemoteWork::find()->where(['id' => $this->allow_remote_id])->one()->name;
    }

    public function getBranchs()
    {
        $branchs = BranchProgram::find()->where(['training_program_id' => $this->id])->all();
        $result = '';
        foreach ($branchs as $branch)
        {
            $result .= $branch->branch->name.'<br>';
        }
        return $result;
    }

    public function getThemesPlan()
    {
        $tp = ThematicPlan::find()->where(['training_program_id' => $this->id])->all();
        $result = count($tp) === $this->capacity ? "" : "<p style='color: red'><i>Несовпадение УТП с объемом программы!</i></p><br>";
        $counter = 1;
        foreach ($tp as $tpOne)
        {
            $result .= '<p>'.$counter.'. '.$tpOne->theme.'</p>';
            $counter += 1;
        }
        return $result;
    }

    public function getStringFocus()
    {
        return Focus::find()->where(['id' => $this->focus_id])->one()->name;
    }

    public function getErrorsWork()
    {
        $errorsList = ProgramErrorsWork::find()->where(['training_program_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $error = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            $result .= 'Внимание, ошибка: ' . $error->number . ' ' . $error->name . '<br>';
        }
        return $result;
    }

    public function afterSave($insert, $changedAttributes)
    {
        //parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if (!(count($changedAttributes) == 0 || count($changedAttributes) == 1 || count($changedAttributes) == 2 && $changedAttributes["actual"] !== null))
        {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменена образовательная программа '.$model->name);
            $edT = new BranchProgram();
            if ($this->isTechnopark == 1)
            {
                $edT->branch_id = 2;
                $edT->training_program_id = $this->id;
                if (count(BranchProgram::find()->where(['branch_id' => 2])->andWhere(['training_program_id' => $this->id])->all()) == 0)
                    $edT->save();
            }
            else
            {
                $edT = BranchProgram::find()->where(['branch_id' => 2])->andWhere(['training_program_id' => $this->id])->one();
                if ($edT !== null)
                    $edT->delete();
            }

            $edQ = new BranchProgram();
            if ($this->isQuantorium == 1)
            {
                $edQ->branch_id = 1;
                $edQ->training_program_id = $this->id;
                if (count(BranchProgram::find()->where(['branch_id' => 1])->andWhere(['training_program_id' => $this->id])->all()) == 0)
                    $edQ->save();
            }
            else
            {
                $edQ = BranchProgram::find()->where(['branch_id' => 1])->andWhere(['training_program_id' => $this->id])->one();
                if ($edQ !== null)
                    $edQ->delete();
            }

            $edC = new BranchProgram();
            if ($this->isCDNTT == 1)
            {
                $edC->branch_id = 3;
                $edC->training_program_id = $this->id;
                if (count(BranchProgram::find()->where(['branch_id' => 3])->andWhere(['training_program_id' => $this->id])->all()) == 0)
                    $edC->save();
            }
            else
            {
                $edC = BranchProgram::find()->where(['branch_id' => 3])->andWhere(['training_program_id' => $this->id])->one();
                if ($edC !== null)
                    $edC->delete();
            }

            $edM = new BranchProgram();
            if ($this->isMobQuant == 1)
            {
                $edM->branch_id = 4;
                $edM->training_program_id = $this->id;
                if (count(BranchProgram::find()->where(['branch_id' => 4])->andWhere(['training_program_id' => $this->id])->all()) == 0)
                    $edM->save();
            }
            else
            {
                $edM = BranchProgram::find()->where(['branch_id' => 4])->andWhere(['training_program_id' => $this->id])->one();
                if ($edM !== null)
                    $edM->delete();
            }

            $edCc = new BranchProgram();
            if ($this->isCod == 1)
            {
                $edCc->branch_id = 7;
                $edCc->training_program_id = $this->id;
                if (count(BranchProgram::find()->where(['branch_id' => 7])->andWhere(['training_program_id' => $this->id])->all()) == 0)
                    $edCc->save();
            }
            else
            {
                $edCc = BranchProgram::find()->where(['branch_id' => 7])->andWhere(['training_program_id' => $this->id])->one();
                if ($edCc !== null)
                    $edCc->delete();
            }
        }
        

        //--------------

        $resp = [new AuthorProgram];
        $resp = $this->authors;

        if ($resp != null)
        {
            for ($i = 0; $i < count($resp); $i++)
            {
                if ($resp[$i]->author_id !== "" && !$this->IsAuthorDuplicate($resp[$i]->author_id)) {
                    $resp[$i]->training_program_id = $this->id;
                    $resp[$i]->save();

                }
            }
        }

        $tp = $this->thematicPlan;

        if ($tp != null)
        {
            for ($i = 0; $i < count($tp); $i++)
            {
                if ($tp[$i]->theme !== "") {
                    $tp[$i]->training_program_id = $this->id;
                    $tp[$i]->save();

                }
            }
        }

        /*var_dump(!(count($changedAttributes) == 0 || count($changedAttributes) == 1 && $changedAttributes["actual"] !== null));
        var_dump($this->id);
        var_dump('<br>');*/

        // тут должны работать проверки на ошибки
        $errorsCheck = new ProgramErrorsWork();
        $errorsCheck->CheckErrorsTrainingProgramWithoutAmnesty($this->id);
    }

    public function uploadEditFiles($upd = null)
    {
        $path = '@app/upload/files/training-program/edit_docs/';
        $result = '';
        $counter = 0;
        if (strlen($this->edit_docs) > 3)
            $counter = count(explode(" ", $this->edit_docs)) - 1;
        foreach ($this->editDocs as $file) {
            $counter++;
            $date = $this->ped_council_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            $filename = 'Ред'.$counter.'_'.$new_date.'_'.$this->name;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->edit_docs = $result;
        else
            $this->edit_docs = $this->edit_docs.$result;
        return true;
    }

    public function uploadDocFile()
    {
        $path = '@app/upload/files/training-program/doc/';
        $date = $this->ped_council_date;
        $new_date = '';
        $filename = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = 'Док.'.$new_date.'_'.$this->name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Яa-zA-Z0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->doc_file = $res.'.'.$this->docFile->extension;
        $this->docFile->saveAs( $path.$res.'.'.$this->docFile->extension);
    }

    public function uploadContractFile()
    {
        $path = '@app/upload/files/training-program/contract/';
        $date = $this->ped_council_date;
        $new_date = '';
        $filename = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = 'Дог.'.$new_date.'_'.$this->name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Яa-zA-Z0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->contract = $res.'.'.$this->contractFile->extension;
        $this->contractFile->saveAs( $path.$res.'.'.$this->contractFile->extension);
    }

    public function beforeSave($insert)
    {
        $this->last_update_id = Yii::$app->user->identity->getId();
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        $branchs = BranchProgram::find()->where(['training_program_id' => $this->id])->all();
        foreach ($branchs as $branch)
            $branch->delete();
        $authors = AuthorProgram::find()->where(['training_program_id' => $this->id])->all();
        foreach ($authors as $author)
            $author->delete();
        $errors = ProgramErrorsWork::find()->where(['training_program_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    private function IsAuthorDuplicate($people_id)
    {
        if (count(AuthorProgram::find()->where(['training_program_id' => $this->id])->andWhere(['author_id' => $people_id])->all()) > 0)
        {
            $fio = People::find()->where(['id' => $people_id])->one();
            Yii::$app->session->addFlash('error', 'Повторное добавление автора: '.
                $fio->secondname.' '.$fio->firstname.' '.$fio->patronymic);
            return true;
        }
        return false;
    }

    public function uploadExcelUtp()
    {
        $tps = ThematicPlanWork::find()->where(['training_program_id' => $this->id])->all();
        foreach ($tps as $tp)
            $tp->delete();
        $this->fileUtp->saveAs('@app/upload/files/training-program/temp/' . $this->fileUtp->name);
        ExcelWizard::WriteUtp($this->fileUtp->name, $this->id);
    }
}
