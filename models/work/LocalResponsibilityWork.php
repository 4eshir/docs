<?php

namespace app\models\work;

use app\models\common\Auditorium;
use app\models\common\Branch;
use app\models\common\DocumentOrder;
use app\models\common\LegacyResponsible;
use app\models\common\LocalResponsibility;
use app\models\extended\AccessTrainingGroup;
use app\models\work\PeopleWork;
use app\models\common\Regulation;
use app\models\common\ResponsibilityType;
use app\models\components\FileWizard;
use Yii;
use yii\helpers\Console;
use yii\helpers\Html;


class LocalResponsibilityWork extends LocalResponsibility
{
    public $filesStr;

    public $start_date;
    public $end_date;
    public $order_id;

    public function rules()
    {
        return [
            [['responsibility_type_id', 'order_id'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            [['responsibility_type_id', 'branch_id', 'auditorium_id', 'quant', 'people_id', 'regulation_id', 'order_id'], 'integer'],
            [['files'], 'string', 'max' => 1000],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleWork::className(), 'targetAttribute' => ['people_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['regulation_id' => 'id']],
            [['responsibility_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResponsibilityType::className(), 'targetAttribute' => ['responsibility_type_id' => 'id']],
            [['filesStr'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'responsibility_type_id' => 'Вид ответственности',
            'responsibilityTypeStr' => 'Вид ответственности',
            'responsibilityTypeStrEx' => 'Вид ответственности',
            'branch_id' => 'Отдел',
            'branchStr' => 'Отдел',
            'branchStrEx' => 'Отдел',
            'auditorium_id' => 'Помещение',
            'auditoriumStr' => 'Помещение',
            'auditoriumStrEx' => 'Помещение',
            'quant' => 'Квант',
            'quantEx' => 'Квант',
            'people_id' => 'Работник',
            'peopleStr' => 'Работник',
            'peopleStrEx' => 'Работник',
            'regulation_id' => 'Положение/инструкция',
            'regulationStr' => 'Положение/инструкция',
            'regulationStrEx' => 'Положение/инструкция',
            'files' => 'Файлы',
            'filesStr' => 'Файлы',
        ];
    }

    public function getResponsibilityTypeStr()
    {
        return Html::a($this->responsibilityType->name, \yii\helpers\Url::to(['responsibility-type/view', 'id' => $this->responsibility_type_id]));
    }

    public function getResponsibilityTypeStrEx()
    {
        return $this->responsibilityType->name;
    }

    public function getBranchStr()
    {
        return Html::a($this->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $this->branch_id]));
    }

    public function getBranchStrEx()
    {
        return $this->branch->name;
    }

    public function getAuditoriumStr()
    {
        return Html::a($this->auditorium->name, \yii\helpers\Url::to(['auditorium/view', 'id' => $this->auditorium_id]));
    }

    public function getAuditoriumStrEx()
    {
        return $this->auditorium->name;
    }

    public function getPeopleStr()
    {
        $fullName = $this->people->secondname.' '.$this->people->firstname.' '.$this->people->patronymic;
        return Html::a($fullName, \yii\helpers\Url::to(['people/view', 'id' => $this->people_id]));
    }

    public function getPeopleStrEx()
    {
        $fullName = $this->people->secondname.' '.$this->people->firstname.' '.$this->people->patronymic;
        return $fullName;
    }


    public function getRegulationStr()
    {
        return Html::a($this->regulation->name, \yii\helpers\Url::to(['regulation/view', 'id' => $this->regulation_id]));
    }

    public function getRegulationStrEx()
    {
        return $this->regulation->name;
    }

    public function getLegacyResp()
    {
        $legs = LegacyResponsibleWork::find()->where(['responsibility_type_id' => $this->responsibility_type_id])
            ->andWhere(['branch_id' => $this->branch_id])->andWhere(['auditorium_id' => $this->auditorium_id])->all();
        $result = '';
        foreach ($legs as $leg)
        {
            $result .= $leg->start_date.' &#9658; ';
            if ($leg->end_date !== null) $result .= $leg->end_date.' '; else $result .= 'н.в. ';
            $result .= Html::a($leg->peopleWork->shortName, \yii\helpers\Url::to(['people/view', 'id' => $leg->people_id]));
            $result .= ' ('.Html::a('Приказ №'.$leg->orderWork->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $leg->order_id, 'c' => 1])).')<br>';
        }
        return $result;
    }

    public function getOrderStr()
    {
        $leg = LegacyResponsible::find()->where(['people_id' => $this->people_id])->andWhere(['responsibility_type_id' => $this->responsibility_type_id])->andWhere(['IS', 'end_date', null])
            ->andWhere(['branch_id' => $this->branch_id])->andWhere(['auditorium_id' => $this->auditorium_id])->one();
        return Html::a(\app\models\work\DocumentOrderWork::find()->where(['id' => $leg->order_id])->one()->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $leg->order_id]));
    }

    public function getOrderStrEx()
    {
        $leg = LegacyResponsible::find()->where(['people_id' => $this->people_id])->andWhere(['responsibility_type_id' => $this->responsibility_type_id])->andWhere(['IS', 'end_date', null])
            ->andWhere(['branch_id' => $this->branch_id])->andWhere(['auditorium_id' => $this->auditorium_id])->one();
        return \app\models\work\DocumentOrderWork::find()->where(['id' => $leg->order_id])->one()->fullName;
    }

    public function uploadFiles($upd = null)
    {
        $path = '@app/upload/files/local-responsibility/';
        $result = '';
        $counter = 0;
        if (strlen($this->files) > 3)
            $counter = count(explode(" ", $this->files)) - 1;
        foreach ($this->filesStr as $file) {
            $counter++;
            $filename = 'Файл'.$counter.'_'.$this->id.'_'.$this->people->secondname.'_'.$this->responsibilityType->name;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->files = $result;
        else
            $this->files = $this->files.$result;
        return true;
    }

    public function detachResponsibility()
    {
        $resp = LocalResponsibility::find()->where(['id' => $this->id])->one();
        $resp->people_id = null;
        $resp->save(false);
        $leg = LegacyResponsible::find()->where(['people_id' => $this->people_id])->andWhere(['responsibility_type_id' => $this->responsibility_type_id])->andWhere(['IS', 'end_date', null])
            ->andWhere(['branch_id' => $this->branch_id])->andWhere(['auditorium_id' => $this->auditorium_id])->one();
        if ($leg !== null)
        {
            $leg->end_date = $this->end_date;
            $leg->save();
        }
    }

    public function beforeSave($insert)
    {
        if ($this->branch_id === "") $this->branch_id = NULL;
        if ($this->auditorium_id === "") $this->auditorium_id = NULL;
        $loc = LocalResponsibility::find()->where(['responsibility_type_id' => $this->responsibility_type_id])->andWhere(['branch_id' => $this->branch_id])->andWhere(['auditorium_id' => $this->auditorium_id])->andWhere(['is not', 'quant', NULL])->all();
        $maxquant = 0;
        foreach ($loc as $nloc)
        {
            if ($nloc->quant > $maxquant)
                $maxquant = $nloc->quant;
        }
        if ($this->quant === "")
            $this->quant = $maxquant+1;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $legSet = LegacyResponsible::find()->where(['people_id' => $this->people_id])->andWhere(['responsibility_type_id' => $this->responsibility_type_id])->andWhere(['IS', 'end_date', null])
            ->andWhere(['branch_id' => $this->branch_id])->andWhere(['auditorium_id' => $this->auditorium_id]);
        if($this->quant != null && $legSet->one() != null)
        {
            $leg = $legSet->one();
            $leg->quant = $this->quant;
            $leg->save();
        }
        if ($this->end_date == "")
        {
            $leg = $legSet->andWhere(['quant'=>$this->quant])->one();
            if ($leg === null) $leg = new LegacyResponsible();
            $leg->people_id = $this->people_id;
            if ($this->start_date !== null) $leg->start_date = $this->start_date;
            $leg->responsibility_type_id = $this->responsibility_type_id;
            $leg->branch_id = $this->branch_id;
            $leg->auditorium_id = $this->auditorium_id;
            $leg->quant = $this->quant;
            $leg->order_id = $this->order_id;
            $leg->end_date = null;
            $leg->save();
        }
    }

    public function beforeDelete()
    {
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}
