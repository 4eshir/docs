<?php

namespace app\models\work;

use app\models\common\Branch;
use app\models\common\Company;
use app\models\common\DocumentIn;
use app\models\common\DocumentOut;
use app\models\common\LocalResponsibility;
use app\models\common\ParticipantAchievement;
use app\models\common\People;
use app\models\common\PeoplePositionBranch;
use app\models\common\Position;
use app\models\common\TeacherParticipant;
use app\models\common\TrainingGroup;
use app\models\null\BranchNull;
use app\models\components\petrovich\Petrovich;
use app\models\extended\AccessTrainingGroup;
use app\models\null\CompanyNull;
use app\models\null\PositionNull;
use Yii;
use yii\helpers\Html;


class PeopleWork extends People
{
    public $stringPosition;

    public $positions;

    public function rules()
    {
        return [
            [['id', 'firstname', 'secondname', 'patronymic'], 'required'],
            [['id', 'company_id', 'position_id', 'branch_id', 'sex'], 'integer'],
            [['firstname', 'secondname', 'patronymic', 'stringPosition', 'short', 'birthdate', 'genitive'], 'string', 'max' => 1000],
            [['id'], 'unique'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    public function getBranchWork()
    {
        $try = $this->hasOne(BranchWork::className(), ['id' => 'branch_id']);
        return $try->all() ? $try : new BranchNull();
    }

    public function getCompanyWork()
    {
        $try = $this->hasOne(CompanyWork::className(), ['id' => 'company_id']);
        return $try->all() ? $try : new CompanyNull();
    }

    public function getPositionWork()
    {
        $try = $this->hasOne(PositionWork::className(), ['id' => 'position_id']);
        return $try->all() ? $try : new PositionNull();
    }

    public function checkForeignKeys()
    {
        $doc_out_signed = DocumentOut::find()->where(['signed_id' => $this->id])->all();
        $doc_out_exec = DocumentOut::find()->where(['executor_id' => $this->id])->all();
        $doc_in_corr = DocumentIn::find()->where(['correspondent_id' => $this->id])->all();
        $doc_in_signed = DocumentIn::find()->where(['signed_id' => $this->id])->all();
        if (count($doc_out_signed) > 0 || count($doc_out_exec) > 0 || count($doc_in_corr) > 0 || count($doc_in_signed) > 0)
        {

            Yii::$app->session->addFlash('error', 'Невозможно удалить человека! Человек включен в существующие документы');
            return false;
        }
        return true;
    }

    public function getFullNameBranch($branch_id)
    {
        $newPosition = "";
        $positions = PeoplePositionBranch::find()->where(['branch_id' => $branch_id])->andWhere(['people_id' => $this->id])->all();
        if (count($positions) == 0) return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
        else if (count($positions) == 1) return $this->secondname.' '.$this->firstname.' '.$this->patronymic.' ('.$positions[0]->position->name.')';
        else
        {
            for ($i = 0; $i !== count($positions) - 1; $i++)
                $newPosition .= $positions[$i]->position->name.', ';
            $newPosition .= $positions[count($positions) - 1]->position->name;
            return $this->secondname.' '.$this->firstname.' '.$this->patronymic.' ('.$newPosition.')';
        }
    }

    public function getShortName()
    {
        return $this->secondname.' '.mb_substr($this->firstname, 0, 1).'.'.mb_substr($this->patronymic, 0, 1).'.';
    }

    public function getGroups()
    {
        return TrainingGroup::find()->where(['teacher_id' => $this->id])->all();
    }

    public function GetSexString()
    {
        if ($this->sex === null) return '---';
        if ($this->sex === 0) return 'Мужской';
        if ($this->sex === 1) return 'Женский';
        if ($this->sex === 2) return 'Другое';
    }

    public function getPositionsWork()
    {
        $pos = PeoplePositionBranch::find()->where(['people_id' => $this->id])->all();
        $result = '';
        foreach ($pos as $posOne)
            $result .= $posOne->position->name . '<br>';
        return $result;
    }



    public function getPositionsList()
    {
        $pos = PeoplePositionBranch::find()->where(['people_id' => $this->id])->all();
        $result = '';
        foreach ($pos as $posOne)
            $result .= $posOne->position->name . ' (' . Html::a($posOne->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $posOne->branch_id])).') <br>';
        return $result;
    }

    public function getRespLinks()
    {
        $resp = LegacyResponsibleWork::find()->where(['people_id' => $this->id])->andWhere(['end_date' => NULL])->all();
        $result = '';
        foreach ($resp as $respOne)
        {
            $loc = LocalResponsibility::find()->where(['responsibility_type_id' => $respOne->responsibility_type_id])->andWhere(['branch_id' => $respOne->branch_id])->andWhere(['auditorium_id' => $respOne->auditorium_id])->andWhere(['quant' => $respOne->quant])->one();
            if ($loc === Null)
                $result .= '<p style="font-style: italic; color: red; display: inline">Ответственность удалена</p>'.'<br>';
            else
                $result .= Html::a($respOne->responsibilityType->name.' '.$respOne->branch->name.' '.$respOne->auditorium->name, \yii\helpers\Url::to(['local-responsibility/view', 'id' => $loc->id])).'<br>';
        }
        return $result;
    }

    public function getGroupsList()
    {
        $groups = TrainingGroup::find()->where(['teacher_id' => $this->id])->all();
        $result = '';
        foreach ($groups as $group)
        {
            $result .= Html::a('Группа '.$group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id])).'<br>';

        }
        return $result;
    }

    public function getAchievements()
    {
        $achieves = ParticipantAchievement::find()
                    ->leftJoin(TeacherParticipant::tableName(), TeacherParticipant::tableName().'.participant_id ='.ParticipantAchievement::tableName().'.participant_id')
                    ->where([TeacherParticipant::tableName().'.teacher_id' => $this->id])
                    ->all();
        foreach ($achieves as $achieveOne)
        {
            $achieveList = $achieveList.Html::a($achieveOne->participant->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $achieveOne->participant_id])).
                ' &mdash; '.$achieveOne->achievment.
                ' '.Html::a($achieveOne->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $achieveOne->foreign_event_id])).' ('.$achieveOne->foreignEvent->start_date.')'.'<br>';
        }
        return $achieveList;
    }

    public function getFullName()
    {
        $positions = '';
        $pos = PeoplePositionBranch::find()->where(['people_id' => $this->id])->all();
        foreach ($pos as $posOne)
            $positions .= $posOne->position->name . ', ';
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic.' ('.substr($positions, 0, -2).')';
    }

    public function getFullNameWithCompany()
    {
        $positions = '';
        $pos = PeopleWork::find()->where(['id' => $this->id])->one();
        $positions .= $pos->company->name.' - '.$pos->position->name;
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic.' ('.$positions.')';
    }

    public function getPositionAndShortFullName()
    {
        // должность и фио в формате "директор В.В.Войков
        $fio = mb_substr($this->firstname, 0, 1) .'. '. mb_substr($this->patronymic, 0, 1) .'. '. $this->genitive;

        $pos = PeoplePositionBranchWork::find()->where(['people_id' => $this->id])->all();

        /* Если нужен список всех должностей
         * $post = [];
        foreach ($pos as $posOne)
            $post [] = $posOne->position_id;
        $post = array_unique($post);    // выкинули все повторы
        */

        $petrovich = new Petrovich(Petrovich::GENDER_MALE);
        $posGenetive = explode(" ", $pos[count($pos)-1]->positionWork->name);
        $posGenetive[0] = mb_strtolower($petrovich->firstname($posGenetive[0], Petrovich::CASE_ACCUSATIVE));

        $result = '';
        foreach ($posGenetive as $word)
            $result .= $word . ' ';

        return $result.$fio;
    }

    public function beforeSave($insert)
    {
        if (strlen($this->short) > 2)
        {
            $current = PeopleWork::find()->where(['id' => $this->id])->one();
            if (strlen($current->short) < 7)
            {
                $similar = PeopleWork::find()->where(['like', 'short', $this->short.'%', false])->andWhere(['!=', 'id', $current->id])->all();
                $this->short .= count($similar) + 1;
            }
            else
                $this->short = $current->short;
        }
        if ($this->stringPosition == '')
            $this->stringPosition = '---';
        $position = Position::find()->where(['name' => $this->stringPosition])->one();
        if ($position !== null)
            $this->position_id = $position->id;
        else
        {
            $position = new Position();
            $position->name = $this->stringPosition;
            $position->save();
            $newPos = Position::find()->where(['name' => $this->stringPosition])->one();
            $this->position_id = $newPos->id;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($this->positions !== null && $this->positions[0]->position_id !== "")
        {
            foreach ($this->positions as $position)
            {
                if ($position->branch_id !== null)
                    $newPPB = PeoplePositionBranch::find()->where(['people_id' => $this->id])
                        ->andWhere(['position_id' => $position->position_id])
                        ->andWhere(['branch_id' => $position->branch_id])->one();
                else
                    $newPPB = PeoplePositionBranch::find()->where(['people_id' => $this->id])
                        ->andWhere(['position_id' => $position->position_id])->one();
                if ($newPPB == null) $newPPB = new PeoplePositionBranch();
                $newPPB->position_id = $position->position_id;
                $newPPB->branch_id = $position->branch_id;
                $newPPB->people_id = $this->id;
                $newPPB->save();
            }
        }

    }
}
