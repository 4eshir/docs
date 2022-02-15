<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\Expire;
use app\models\common\People;
use app\models\common\Regulation;
use app\models\common\Responsible;
use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;


class DocumentOrderWork extends DocumentOrder
{
    public $scanFile;
    public $docFiles;
    public $responsibles;
    public $expires;
    public $expires2;
    public $people_arr;

    public $signedString;
    public $executorString;
    public $bringString;
    public $registerString;

    public $allResp;

    public $nomenclature_number;

    public $groups_check;

    public $archive_check;

    public $archive_number;


    public function rules()
    {
        return [
            [['scanFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true],
            [['docFiles'], 'file', 'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['signedString', 'executorString', 'bringString', 'registerString', 'documentNumberString'], 'string'],
            [['order_number', 'order_name', 'order_date', 'signed_id', 'bring_id', 'executor_id', 'register_id',
              'signedString', 'executorString', 'bringString'], 'required'],
            [['signed_id', 'bring_id', 'executor_id', 'register_id', 'order_postfix', 'order_copy_id', 'type', 'nomenclature_id', 'study_type', 'archive_check' ], 'integer'],
            [['order_date', 'allResp', 'groups_check'], 'safe'],
            [['state'], 'boolean'],
            [['order_name', 'scan', 'key_words'], 'string', 'max' => 1000],
            [['nomenclature_number', 'archive_number'], 'string'],
            [['order_number'], 'string', 'max' => 100],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
        ];
    }

    public function getGroupsLink()
    {
        $orders = OrderGroupWork::find()->where(['document_order_id' => $this->id])->all();
        $result = '';
        foreach ($orders as $order)
        {
            $result .= Html::a($order->trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $order->training_group_id]));
            $result .= '<br>';
        }
        return $result;
    }

    public function getDocumentNumberString()
    {
        if ($this->order_copy_id == 0)
            return $this->order_number;
        if ($this->order_postfix == null)
            return $this->order_number.'/'.$this->order_copy_id;
        else
            return $this->order_number.'/'.$this->order_copy_id.'/'.$this->order_postfix;
    }

    public function getExpireOrders2()
    {
        $changes = ExpireWork::find()->where(['expire_type' => 2])->andWhere(['active_regulation_id' => $this->id])->andWhere(['not', ['expire_order_id' => null]])->all();
        $result = "";
        foreach ($changes as $change)
        {
            $doc_num = 0;
            if ($change->expireOrderWork->order_postfix == null)
                $doc_num = $change->expireOrderWork->order_number.'/'.$change->expireOrderWork->order_copy_id;
            else
                $doc_num = $change->expireOrderWork->order_number.'/'.$change->expireOrderWork->order_copy_id.'/'.$change->expireOrderWork->order_postfix;
            $result .= Html::a('Приказ №' . $doc_num . ' ' . $change->expireOrderWork->order_name, \yii\helpers\Url::to(['document-order/view', 'id' => $change->expire_order_id])) . '<br>';
        }

        $changes = ExpireWork::find()->where(['expire_type' => 2])->andWhere(['active_regulation_id' => $this->id])->andWhere(['not', ['expire_regulation_id' => null]])->all();
        foreach ($changes as $change)
        {
            $result .= Html::a('Положение ' . $change->expireRegulationWork->name, \yii\helpers\Url::to(['regulation/view', 'id' => $change->expire_regulation_id])) . '<br>';
        }

        return $result;
    }

    public function getChangeDocs()
    {
        $changes = ExpireWork::find()->where(['expire_type' => 2])->andWhere(['expire_order_id' => $this->id])->all();
        $result = "";
        foreach ($changes as $change)
        {
            $doc_num = 0;
            if ($change->activeRegulationWork->order_postfix == null)
                $doc_num = $change->activeRegulationWork->order_number.'/'.$change->activeRegulationWork->order_copy_id;
            else
                $doc_num = $change->activeRegulationWork->order_number.'/'.$change->activeRegulationWork->order_copy_id.'/'.$change->activeRegulationWork->order_postfix;
            $result .= Html::a('Приказ №' . $doc_num . ' ' . $change->activeRegulationWork->order_name, \yii\helpers\Url::to(['document-order/view', 'id' => $change->active_regulation_id])) . '<br>';
        }
        return $result;
    }

    public function beforeSave($insert)
    {
        $fioSigned = explode(" ", $this->signedString);
        $fioExecutor = explode(" ", $this->executorString);
        $fioRegister = explode(" ", $this->registerString);
        $fioBring = explode(" ", $this->bringString);

        $fioSignedDb = People::find()->where(['secondname' => $fioSigned[0]])
            ->andWhere(['firstname' => $fioSigned[1]])
            ->andWhere(['patronymic' => $fioSigned[2]])->one();
        $fioExecutorDb = People::find()->where(['secondname' => $fioExecutor[0]])
            ->andWhere(['firstname' => $fioExecutor[1]])
            ->andWhere(['patronymic' => $fioExecutor[2]])->one();
        $fioRegisterDb = People::find()->where(['secondname' => $fioRegister[0]])
            ->andWhere(['firstname' => $fioRegister[1]])
            ->andWhere(['patronymic' => $fioRegister[2]])->one();
        $fioBringDb = People::find()->where(['secondname' => $fioBring[0]])
            ->andWhere(['firstname' => $fioBring[1]])
            ->andWhere(['patronymic' => $fioBring[2]])->one();

        if ($fioSignedDb !== null)
            $this->signed_id = $fioSignedDb->id;

        if ($fioExecutorDb !== null)
            $this->executor_id = $fioExecutorDb->id;

        if ($fioRegisterDb !== null)
            $this->register_id = $fioRegisterDb->id;

        if ($fioRegisterDb !== null)
            $this->bring_id = $fioBringDb->id;

        $this->register_id = Yii::$app->user->identity->getId();
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/order/scan/';
        $date = $this->order_date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = '';
        if ($this->order_postfix == null)
            $filename = 'П.'.$new_date.'_'.$this->order_number.'-'.$this->order_copy_id.'_'.$this->order_name;
        else
            $filename = 'П.'.$new_date.'_'.$this->order_number.'-'.$this->order_copy_id.'-'.$this->order_postfix.'_'.$this->order_name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->scan = $res . '.' . $this->scanFile->extension;
        $this->scanFile->saveAs( $path . $res . '.' . $this->scanFile->extension);
    }

    public function uploadDocFiles($upd = null)
    {
        $path = '@app/upload/files/order/docs/';
        $result = '';
        $counter = 0;
        if (strlen($this->doc) > 4)
            $counter = count(explode(" ", $this->doc)) - 1;
        foreach ($this->docFiles as $file) {
            $counter++;
            $date = $this->order_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->order_postfix == null)
                $filename = $counter.'_Пр.'.$new_date.'_'.$this->order_number.'-'.$this->order_copy_id.'_'.$this->order_name;
            else
                $filename = $counter.'_Пр.'.$new_date.'_'.$this->order_number.'-'.$this->order_copy_id.'-'.$this->order_postfix.'_'.$this->order_name;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
            $res = FileWizard::CutFilename($res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->doc = $result;
        else
            $this->doc = $this->doc.$result;
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        //прикрепление и открепление приказов к/от групп(-ам)
        if ($this->groups_check !== null && count($this->groups_check) > 0)
        {
            $groupsId = [];
            foreach ($this->groups_check as $group_check)
            {
                $groupsId[] = $group_check;
                $order = OrderGroupWork::find()->where(['training_group_id' => $group_check])->andWhere(['document_order_id' => $this->id])->one();
                if ($order === null)
                {
                    $order = new OrderGroupWork();
                    $order->training_group_id = $group_check;
                    $order->document_order_id = $this->id;
                    $order->save();
                }
            }

            $delGroups = TrainingGroupWork::find()->where(['order_stop' => 0])->andWhere(['archive' => 0])->andWhere(['not in', 'id', $groupsId])->all();
            foreach ($delGroups as $delGroup)
            {
                $order = OrderGroupWork::find()->where(['training_group_id' => $delGroup->id])->andWhere(['document_order_id' => $this->id])->one();
                if ($order !== null)
                    $order->delete();
            }
        }

        $expireOrder = [new Expire];
        $expireOrder = $this->expires;
        if ($expireOrder !== null && (strlen($expireOrder[0]->expire_regulation_id) != 0 || strlen($expireOrder[0]->expire_order_id) != 0))
        {
            for ($i = 0; $i < count($expireOrder); $i++)
            {
                if ($expireOrder[$i]->expire_order_id !== '')
                {
                    $expireOrder[$i]->document_type_id = 1;
                    $orders = DocumentOrder::find()->where(['id' => $expireOrder[$i]->expire_order_id])->all();

                    foreach ($orders as $orderOne)
                    {
                        $orderOne->state = false;
                        $orderOne->save(false);

                    }
                }
                else
                {
                    $expireOrder[$i]->document_type_id = 4;
                }

                $expireOrder[$i]->active_regulation_id = $this->id;
                $reg = Regulation::find()->where(['id' => $expireOrder[$i]->expire_regulation_id])->all();

                if (count($reg) > 0)
                {
                    foreach ($reg as $regOne)
                    {
                        $regOne->state = 'Утратило силу';
                        $regOne->save(false);
                    }
                }

                //var_dump($expireOrder[$i]->expire_type);
                $expireOrder[$i]->save(false);
            }
        }

        
        if ($this->allResp != 1)
        {
            $resp = [new Responsible];
            $resp = $this->responsibles;

            if ($resp != null)
            {
                for ($i = 0; $i < count($resp); $i++)
                {
                    $split = explode(" ", $resp[$i]->fio);
                    $p_id = People::find()->where(['firstname' => $split[1]])->andWhere(['secondname' => $split[0]])
                        ->andWhere(['patronymic' => $split[2]])->one()->id;
                    if (!$this->IsResponsibleDuplicate($p_id)) {
                        $resp[$i]->people_id = $p_id;
                        $resp[$i]->document_order_id = $this->id;
                        $resp[$i]->save();
                    }
                }
            }
            
        }
        else
        {
            $peoples = People::find()->where(['company_id' => 8])->all();
            for ($i = 0; $i < count($peoples); $i++)
            {
                if (!$this->IsResponsibleDuplicate($peoples[$i]->id)) {
                    $respOne = new Responsible();
                    $respOne->people_id = $peoples[$i]->id;
                    $respOne->document_order_id = $this->id;
                    $respOne->save();
                }
            }
        }

        // если в группе была ошибка об отсутствии приказа, то тут она уйдет
        if ($this->groups_check !== null && count($this->groups_check) > 0)
        {
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckOrderTrainingGroup($this->groups_check);
        }

        // тут должны работать проверки на ошибки
        $errorsCheck = new OrderErrorsWork();
        $errorsCheck->CheckErrorsDocumentOrderWithoutAmnesty($this->id);
    }

    public function beforeDelete()
    {
        $resp = ResponsibleWork::find()->where(['document_order_id' => $this->id])->all();
        foreach ($resp as $respOne)
            $respOne->delete();

        $groups = OrderGroupWork::find()->where(['document_order_id' => $this->id])->all();
        foreach ($groups as $group)
            $group->delete();

        if ($this->groups_check !== null && count($this->groups_check) > 0)
        {
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckOrderTrainingGroup($this->groups_check);
        }

        $errors = OrderErrorsWork::find()->where(['document_order_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    private function IsResponsibleDuplicate($people_id)
    {
        if (count(Responsible::find()->where(['document_order_id' => $this->id])->andWhere(['people_id' => $people_id])->all()) > 0)
        {
            $fio = People::find()->where(['id' => $people_id])->one();
            Yii::$app->session->addFlash('error', 'Повторное добавление ответственного: '.
                $fio->secondname.' '.$fio->firstname.' '.$fio->patronymic);
            return true;
        }
        return false;
    }

    public function getDocumentNumber()
    {
        if (strtotime($this->order_date) < strtotime('2021-01-01'))
        {
            $this->order_copy_id = 0;
            Yii::$app->session->addFlash('warning', 'Добавлен архивный приказ. Дата приказа '.$this->order_date);

            return;
        }


        $docs = DocumentOrder::find()->orderBy(['order_date' => SORT_DESC])->all();

        if (date('Y') !== substr($docs[0]->order_date, 0, 4))
            $this->order_copy_id = 1;
        else
        {
            $docs = DocumentOrder::find()->where(['order_number' => $this->order_number])->andWhere(['like', 'order_date', date('Y')])->andWhere(['!=', 'type', '10'])->orderBy(['order_copy_id' => SORT_ASC, 'order_postfix' => SORT_ASC])->all();

            if (end($docs)->order_date > $this->order_date && $this->order_name != 'Резерв')
            {
                $tempId = 0;
                $tempPre = 0;
                if (count($docs) == 0)
                    $tempId = 1;
                for ($i = count($docs) - 1; $i >= 0; $i--)
                {
                    if ($docs[$i]->order_date <= $this->order_date)
                    {
                        $tempId = $docs[$i]->order_copy_id;
                        if ($docs[$i]->order_postfix != null)
                            $tempPre = $docs[$i]->order_postfix + 1;
                        else
                            $tempPre = 1;
                        break;
                    }
                }

                $this->order_copy_id = $tempId;
                $this->order_postfix = $tempPre;
                Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->order_number.'/'.$this->order_copy_id.'/'.$this->order_postfix);
            }
            else
            {
                if (count($docs) == 0)
                    $this->order_copy_id = 1;
                else
                {
                    $this->order_copy_id = end($docs)->order_copy_id + 1;
                }
            }
        }

        /*$max = DocumentOut::find()->max('document_number');
        if ($max == null)
            $max = 1;
        else
            $max = $max + 1;
        return $max;*/
    }

    public function getFullName()
    {
        if ($this->order_postfix !== null)
            return $this->order_number.'/'.$this->order_copy_id.'/'.$this->order_postfix.' '.$this->order_name;
        else
            return $this->order_number.'/'.$this->order_copy_id.' '.$this->order_name;
    }

    public function checkForeignKeys()
    {
        $regs1 = Expire::find()->where(['active_regulation_id' => $this->id])->all();
        $regs2 = Expire::find()->where(['expire_regulation_id' => $this->id])->all();
        $regs3 = Regulation::find()->where(['order_id' => $this->id])->all();
        if (count($regs1) > 0 || count($regs2) > 0 || count($regs3) > 0)
            return true;
        else
            return false;
    }

    public function getStateAndColor()
    {
        if ($this->state == 1)
        {
            $change2 = $this->getExpireOrders2();
            if ($change2 !== "")
                return 'Вносит изменения в документы: ' . $change2;

            return 'Актуален';
        }
        else
        {
            $change = $this->getChangeDocs();
            if ($change !== "")
                return 'Был изменен документами: ' . $change;

            $exp = \app\models\work\ExpireWork::find()->where(['expire_order_id' => $this->id])->one();
            $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $exp->active_regulation_id])->one();
            $doc_num = 0;
            if ($order->order_postfix == null)
                $doc_num = $order->order_number.'/'.$order->order_copy_id;
            else
                $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
            return 'Утратил силу в связи с приказом '.Html::a('№'.$doc_num, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
        }
    }

    public function getErrorsWork()
    {
        $errorsList = OrderErrorsWork::find()->where(['document_order_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $errorName = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            if ($errors->critical == 1)
                $result .= 'Внимание, КРИТИЧЕСКАЯ ошибка: ' . $errorName->number . ' ' . $errorName->name . '<br>';
            else $result .= 'Внимание, ошибка: ' . $errorName->number . ' ' . $errorName->name . '<br>';
        }
        return $result;
    }
}