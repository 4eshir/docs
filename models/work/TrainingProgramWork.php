<?php

namespace app\models\work;

use app\models\common\AuthorProgram;
use app\models\common\BranchProgram;
use app\models\common\Focus;
use app\models\common\People;
use app\models\common\ThematicDirection;
use app\models\common\ThematicPlan;
use app\models\common\TrainingProgram;
use app\models\components\FileWizard;
use Yii;


class TrainingProgramWork extends TrainingProgram
{
    public $isTechnopark;
    public $isQuantorium;
    public $isCDNTT;
    public $isMobQuant;

    public $authors;
    public $thematicPlan;

    public $docFile;
    public $editDocs;

    public function rules()
    {
        return [
            [['name', 'author_id', 'focus', 'hour_capacity'], 'required'],
            [['ped_council_date'], 'safe'],
            [['focus_id', 'author_id', 'capacity', 'student_left_age', 'student_right_age', 'allow_remote', 'isCDNTT', 'isQuantorium', 'isTechnopark', 'isMobQuant', 'thematic_direction_id', 'level', 'hour_capacity'], 'integer'],
            [['name', 'ped_council_number', 'doc_file', 'edit_docs', 'key_words'], 'string', 'max' => 1000],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['thematic_direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThematicDirection::className(), 'targetAttribute' => ['thematic_direction_id' => 'id']],
            [['docFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true],
            [['editDocs'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'ped_council_date' => 'Дата педагогического совета',
            'ped_council_number' => 'Номер протокола педагогического совета',
            'author_id' => 'Составитель',
            'thematic_direction_id' => 'Тематическое направление',
            'level' => 'Уровень сложности',
            'authorsList' => 'Составители',
            'capacity' => 'Объем, ак. час.',
            'student_left_age' => 'Мин. возраст учащихся, лет',
            'student_right_age' => 'Макс. возраст учащихся, лет',
            'studentAge' => 'Возраст учащихся, лет',
            'focus_id' => 'Направленность',
            'stringFocus' => 'Направленность',
            'allow_remote' => 'С применением дистанционных технологий',
            'doc_file' => 'Документ программы',
            'docFile' => 'Документ программы',
            'edit_docs' => 'Редактируемые документы',
            'editDocs' => 'Редактируемые документы',
            'key_words' => 'Ключевые слова',
            'isCDNTT' => 'ЦДНТТ',
            'isQuantorium' => 'Кванториум',
            'isTechnopark' => 'Технопарк',
            'isMobQuant' => 'Мобильный кванториум',
            'branchs' => 'Отдел(-ы) - место реализации',
            'hour_capacity' => 'Длительность 1 академического часа в минутах',
        ];
    }

    public function getFullName()
    {
        $authors = AuthorProgram::find()->where(['training_program_id' => $this->id])->all();
        $result = '';
        foreach ($authors as $author)
        {
            $result .= $author->author->shortName.', ';
        }
        return $this->name.' ('.$result.')';
    }

    public function getAuthorWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'author_id']);
    }


    public function getFocus()
    {
        return $this->hasOne(Focus::className(), ['id' => 'author_id']);
    }

    public function getThematicDirection()
    {
        return $this->hasOne(ThematicDirection::className(), ['id' => 'thematic_direction_id']);
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
        return $this->allow_remote == 0 ? 'Нет' : 'Да';
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
        $result = count($tp) === $this->capacity ? "" : "<font color=red><i>Несовпадение УТП с объемом программы!</i></font><br>";
        foreach ($tp as $tpOne)
        {
            $result .= $tpOne->theme.'<br>';
        }
        return $result;
    }

    public function getStringFocus()
    {
        return Focus::find()->where(['id' => $this->focus_id])->one()->name;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

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
    }

    public function uploadEditFiles($upd = null)
    {
        $path = '@app/upload/files/program/edit_docs/';
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
        $path = '@app/upload/files/program/doc/';
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

    public function beforeDelete()
    {
        $branchs = BranchProgram::find()->where(['training_program_id' => $this->id])->all();
        foreach ($branchs as $branch)
            $branch->delete();
        $authors = AuthorProgram::find()->where(['training_program_id' => $this->id])->all();
        foreach ($authors as $author)
            $author->delete();
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
}
