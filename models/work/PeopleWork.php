<?php

namespace app\models\common;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "people".
 *
 * @property int $id
 * @property string $firstname
 * @property string $secondname
 * @property string $patronymic
 * @property string $short
 * @property int|null $company_id
 * @property int|null $position_id
 * @property int|null $branch_id
 * @property string $birthdate
 * @property int $sex
 *
 * @property Company $company
 * @property Position $position
 * @property Branch $branch
 */
class People extends \yii\db\ActiveRecord
{
    public $stringPosition;

    public $positions;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'firstname', 'secondname', 'patronymic'], 'required'],
            [['id', 'company_id', 'position_id', 'branch_id', 'sex'], 'integer'],
            [['firstname', 'secondname', 'patronymic', 'stringPosition', 'short', 'birthdate'], 'string', 'max' => 1000],
            [['id'], 'unique'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'secondname' => 'Secondname',
            'patronymic' => 'Patronymic',
            'short' => 'Уникальный идентификатор',
            'company_id' => 'Company ID',
            'position_id' => 'Position ID',
            'branch_id' => 'Отдел по трудовому договору',
            'birthdate' => 'Дата рождения',
            'sex' => 'Пол',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
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

    /**
     * Gets query for [[TrainingProgramParticipants]].
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic.' ('.$this->position->name.')';
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
        $resp = LocalResponsibility::find()->where(['people_id' => $this->id])->all();
        $result = '';
        foreach ($resp as $respOne)
            $result .= Html::a($respOne->responsibilityType->name.' '.$respOne->branch->name.' '.$respOne->auditorium->name, \yii\helpers\Url::to(['local-responsibility/view', 'id' => $respOne->id])).'<br>';
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

    public function beforeSave($insert)
    {
        if (strlen($this->short) > 2)
        {
            $current = People::find()->where(['id' => $this->id])->one();
            if (strlen($current->short) < 7)
            {
                $similar = People::find()->where(['like', 'short', $this->short.'%', false])->andWhere(['!=', 'id', $current->id])->all();
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