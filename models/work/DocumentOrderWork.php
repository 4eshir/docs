<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\DocumentOrderSupplement;
use app\models\common\Expire;
use app\models\common\ForeignEventParticipants;
use app\models\common\ParticipantFiles;
use app\models\common\People;
use app\models\common\User;
use app\models\common\Regulation;
use app\models\common\Responsible;
use app\models\components\FileWizard;
use app\models\null\PeopleNull;
use app\models\null\UserNull;
use Psr\Log\NullLogger;
use Yii;
use yii\helpers\Html;
use app\models\components\Logger;
use yii\web\UploadedFile;


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
    public $creatorString;

    public $allResp;

    public $nomenclature_number;

    public $groups_check;
    public $participants_check;
    public $new_groups_check;

    public $archive_check;

    public $archive_number;

    public $foreign_event;
    public $supplement;
    public $participants;



    public function rules()
    {
        return [
            [['scanFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true],
            [['docFiles'], 'file', 'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['signedString', 'executorString', 'bringString', 'creatorString', 'documentNumberString'], 'string'],
            [['order_number', 'order_name', 'order_date', 'signed_id', 'bring_id', 'executor_id', 'creator_id',
              'signedString', 'executorString', 'bringString'], 'required'],
            [['signed_id', 'bring_id', 'executor_id', 'creator_id', 'last_edit_id', 'order_postfix', 'order_copy_id', 'type', 'nomenclature_id', 'archive_check' ], 'integer'],
            [['order_date', 'allResp', 'groups_check', 'participants_check', 'new_groups_check', 'study_type', 'foreign_event', 'supplement', 'participants'], 'safe'],
            [['state'], 'boolean'],
            [['order_name', 'scan', 'key_words'], 'string', 'max' => 1000],
            [['nomenclature_number', 'archive_number'], 'string'],
            [['order_number'], 'string', 'max' => 100],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
            [['foreign_event'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventWork::className(), 'targetAttribute' => ['id' => 'order_participation_id']],
            //[['supplement'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrderSupplementWork::className(), 'targetAttribute' => ['id' => 'document_order_id']],
            [['participantsFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true],
        ];
    }

    public function getCreatorWork()
    {
        $try = $this->hasOne(UserWork::className(), ['id' => 'creator_id']);
        return $try->all() ? $try : new UserNull();
    }

    public function getLastEditWork()
    {
        $try = $this->hasOne(UserWork::className(), ['id' => 'last_edit_id']);
        return $try->all() ? $try : new UserNull();
    }

    public function getChangeDocFile()
    {
        $split = explode(" ", $this->doc);
        $result = '';
        if (count($split) > 1)
            for ($i = 0; $i < count($split); $i++)
                $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $split[$i], 'modelId' => $this->id, 'type' => 'docs'])).'<br>';
        else
        {
            if ($this->type === 0 || $this->type == 11 || $this->type == 2)   // учебный или об участии
            {
                $view = NomenclatureWork::find()->where(['number' => $this->order_number])->andWhere(['actuality' => 0])->one();
                $result = Html::a("Сгенерировать файл", \yii\helpers\Url::to(['document-order/generation-word', 'order_id' => $this->id, 'type' => $view->type]), ['class'=>'btn btn-success']);
            }
        }
        return $result;
    }


    public function getBringWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'bring_id']);
        return $try->all() ? $try : new PeopleNull();
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

    public function getParticipantsLink()
    {
        $pasta = OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->where(['orderGroup.document_order_id' => $this->id])->all();
        $flag = 0;
        $result = '';
        foreach ($pasta as $macaroni)
        {
            if ($macaroni->link_id !== NULL)
            {
                $flag = 1;
                break;
            }
            $result .= $macaroni->getParticipantAndGroup();
            $result .= '<br>';
        }

        if ($flag !== 0)
        {
            $result = '';
            foreach ($pasta as $macaroni)
            {
                if ($macaroni->link_id !== NULL)
                {
                    $result .= $macaroni->getParticipantDefectors();
                    $result .= '<br>';
                }
            }
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
        $fioCreator = explode(" ", $this->creatorString);
        $fioBring = explode(" ", $this->bringString);

        if (mb_substr($this->order_name, 0, 12) === 'О зачислении')
        {
            if ($this->groups_check != NULL)
                if (count($this->groups_check) > 1)
                    $this->order_name = 'О зачислении на обучение по дополнительным общеразвивающим программам';
                else
                    $this->order_name = 'О зачислении на обучение по дополнительной общеразвивающей программе';
        }

        if (mb_substr($this->order_name, 0, 10) === 'Об участии' && $this->type === 2)
        {
            $pos = strripos($this->foreign_event['name'], '"');
            if ($pos && $this->foreign_event['name'] !== null) $this->foreign_event['name'] = str_replace(' "', ' «', $this->foreign_event['name']);
            $pos = strripos($this->foreign_event['name'], '"');
            if ($pos && $this->foreign_event['name'] !== null) $this->foreign_event['name'] = str_replace('"', '»', $this->foreign_event['name']);

            $this->order_name = 'Об участии в мероприятии «' . $this->foreign_event['name'] . '»';
            $arrName = mb_str_split($this->order_name);
            $lenght = mb_strlen($this->order_name);
            if ($arrName[$lenght-1] == $arrName[$lenght-2]) $this->order_name = mb_substr($this->order_name, 0, -1);
        }


        $fioSignedDb = People::find()->where(['secondname' => $fioSigned[0]])
            ->andWhere(['firstname' => $fioSigned[1]])
            ->andWhere(['patronymic' => $fioSigned[2]])->one();
        $fioExecutorDb = People::find()->where(['secondname' => $fioExecutor[0]])
            ->andWhere(['firstname' => $fioExecutor[1]])
            ->andWhere(['patronymic' => $fioExecutor[2]])->one();
        $fioCreatorDb = People::find()->where(['secondname' => $fioCreator[0]])
            ->andWhere(['firstname' => $fioCreator[1]])
            ->andWhere(['patronymic' => $fioCreator[2]])->one();
        $fioBringDb = People::find()->where(['secondname' => $fioBring[0]])
            ->andWhere(['firstname' => $fioBring[1]])
            ->andWhere(['patronymic' => $fioBring[2]])->one();

        if ($fioSignedDb !== null)
            $this->signed_id = $fioSignedDb->id;

        if ($fioExecutorDb !== null)
            $this->executor_id = $fioExecutorDb->id;

        if ($fioCreatorDb !== null)
            $this->creator_id = $fioCreatorDb->id;

        if ($fioBringDb !== null)
            $this->bring_id = $fioBringDb->id;

        $this->last_edit_id = Yii::$app->user->identity->getId();

        if ($this->type === 1 || $this->type == 10 || mb_substr($this->order_name, 0, 12) === 'О зачислении')
            $this->study_type = NULL;

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/document-order/scan/';
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

        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен скан документа ' . $filename . ' в приказ (id=' .$this->id . ')');
    }

    public function uploadDocFiles($upd = null)
    {
        $path = '@app/upload/files/document-order/docs/';
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

        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен редактируемый документ ' . $filename . ' в приказ (id=' .$this->id . ')');

        return true;
    }

    private function uploadTeamName($foreign_event_id)
    {
        $teamName = [];
        $teamNameException = [];

        $teamNameaExisting = TeamNameWork::find()->where(['foreign_event_id' => $foreign_event_id])->all();
        foreach ($teamNameaExisting as $one)
            $teamNameException[] = $one->name;

        foreach ($this->participants as $partOne)
            if (!in_array($partOne->team, $teamName) && !in_array($partOne->team, $teamNameException) && $partOne->team != NULL && $partOne->team != '--' && $partOne->team != 'NULL')
                $teamName[] = $partOne->team;

        foreach ($teamName as $oneTeam)
        {
            $team = new TeamNameWork();
            $team->name = $oneTeam;
            $team->foreign_event_id = $foreign_event_id;
            $team->save();
        }
    }

    private function cleanArrParticipant($foreign_event_id)
    {
        $arrPart = [];
        $partsBD = TeacherParticipantWork::find()->where(['foreign_event_id' => $foreign_event_id])->all();

        foreach ($this->participants as $partOne)
        {
            $flag = true;   // индикатор уникальности записи факта участия

            foreach ($partsBD as $suspicious)   // ищем дубликаты среди новых и старых записей из БД
            {
                if ($suspicious->participant_id == $partOne->fio && $suspicious->nomination == $partOne->nomination && $suspicious->teamNameString == $partOne->team && $suspicious->focus == $partOne->focus)
                {
                    $flag = false;
                    break;
                }
            }

            foreach ($arrPart as $suspicious)   // ищем дубликаты в массиве участников из заполненной формы
                if ($suspicious->fio == $partOne->fio && $suspicious->nomination == $partOne->nomination && $suspicious->team == $partOne->team && $suspicious->focus == $partOne->focus)
                {
                    $flag = false;
                    break;
                }

            if ($flag)  // уникальные записи добавим в БД
                $arrPart[] = $partOne;
            else
                Yii::$app->session->addFlash('warning', 'Попытка добавления дубликата акта участия');
        }

        $this->participants = $arrPart;
    }

    private function uploadTeacherParticipants($foreign_event_id)
    {
        if ($this->participants !== null)
        {
            $this->cleanArrParticipant($foreign_event_id);

            foreach ($this->participants as $key => $participantOne)
            {
                $part = new TeacherParticipantWork();
                $part->foreign_event_id = $foreign_event_id;
                $part->participant_id = $participantOne->fio;
                $part->teacher_id = $participantOne->teacher;
                $part->teacher2_id = $participantOne->teacher2;
                $part->focus = $participantOne->focus;
                $part->allow_remote_id = $participantOne->allow_remote_id;
                if ($participantOne->nomination !== 'NULL' && $participantOne->nomination !== '--')
                    $part->nomination = $participantOne->nomination;
                $tpbs = [];
                if ($participantOne->branch !== "")
                    for ($i = 0; $i < count($participantOne->branch); $i++)
                    {
                        $tpb = new TeacherParticipantBranchWork();
                        $tpb->branch_id = $participantOne->branch[$i];
                        $tpbs[] = $tpb;
                    }
                $part->teacherParticipantBranches = $tpbs;
                $part->branchs = $participantOne->branch;
                $part->save();

                if ($participantOne->team !== NULL && $participantOne->team !== '--' && $participantOne->team !== 'NULL')
                {
                    $teamId = TeamNameWork::find()->where(['foreign_event_id' => $foreign_event_id])->andWhere(['name' => $participantOne->team])->one();

                    $team = new TeamWork();
                    $team->teacher_participant_id = $part->id;
                    $team->team_name_id = $teamId->id;
                    $team->save();
                }

                if ($participantOne->fileString != NULL)
                {
                    $partFile = new ParticipantFilesWork();
                    $partFile->teacher_participant_id = $part->id;
                    $partFile->filename = $participantOne->fileString;
                    $partFile->save();
                }
            }
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $flashMessage = 'Прикрепление ';

        $nom = NomenclatureWork::find()->where(['number' => $this->order_number])->andWhere(['actuality' => 0])->one();
        $status = $nom->type;    // 0 - зачислен, 1 - отчислен, 2 - перевод
        $gr = TrainingGroupWork::find();

        //прикрепление и открепление приказов к/от групп(-ам)
        if ($this->groups_check[0] !== 'nope')   // тут было условие которое ловило баг, теперь всё работает как надо
        {
            $groupsId = [];
            if ($this->groups_check !== null)
                foreach ($this->groups_check as $group_check)
            {
                $groupsId[] = $group_check;
                $order = OrderGroupWork::find()->where(['training_group_id' => $group_check])->andWhere(['document_order_id' => $this->id])->one();
                $grName = $gr->where(['id' => $group_check])->one();
                if ($order === null)
                {
                    $order = new OrderGroupWork();
                    $order->training_group_id = $group_check;
                    $order->document_order_id = $this->id;
                    Logger::WriteLog(Yii::$app->user->identity->getId(),
                        'К приказу '.$this->order_name . ' № ' . $this->order_number . '/' . $this->order_copy_id . (empty($this->order_postfix) ? '/' . $this->order_postfix : '') . ' добавлена учебная группа ' . $grName->number);
                    $order->save();
                }
            }

            if ($this->groups_check === null)
                $delGroups = TrainingGroupWork::find()->where(['order_stop' => 0])->andWhere(['archive' => 0])->all();
            else
                $delGroups = TrainingGroupWork::find()->where(['order_stop' => 0])->andWhere(['archive' => 0])->andWhere(['not in', 'id', $groupsId])->all();
            foreach ($delGroups as $delGroup)
            {
                $order = OrderGroupWork::find()->where(['training_group_id' => $delGroup->id])->andWhere(['document_order_id' => $this->id])->one();
                $grName = $gr->where(['id' => $delGroup->id])->one();
                if ($order !== null)
                {
                    /**/ // удаление из связки
                    $pasta = OrderGroupParticipantWork::find()->where(['order_group_id' => $order->id])->all();

                    foreach ($pasta as $macaroni)
                    {
                        $tempId = NULL;

                        if ($macaroni->status == 2) // перевод
                        {
                            $defector = OrderGroupParticipantWork::find()->where(['link_id' => $macaroni->id])->one();
                            $tempId = $defector->link_id;
                            $edit = TrainingGroupParticipantWork::find()->where(['id' => $macaroni->group_participant_id])->one();
                            if ($edit !== NULL)
                            {
                                $edit->status = 0;
                                $edit->save();
                            }
                            if ($defector !== NULL)
                                $defector->delete();
                        }

                        if ($macaroni->link_id !== NULL)       // перевод-зачисление
                        {
                            $noDefector = OrderGroupParticipantWork::find()->where(['id' => $macaroni->link_id])->one();
                            $tempId = $macaroni->link_id;
                            $edit = TrainingGroupParticipantWork::find()->where(['id' => $noDefector->group_participant_id])->one();
                            if ($edit !== NULL)
                            {
                                $edit->status = 0;
                                $edit->save();
                            }
                            if ($noDefector !== NULL)
                                $noDefector->delete();
                        }

                        if ($macaroni->status == 1) // отчисление
                        {
                            $edit = TrainingGroupParticipantWork::find()->where(['id' => $macaroni->group_participant_id])->one();
                            if ($edit !== NULL)
                            {
                                $edit->status = 0;
                                $edit->save();
                            }
                        }

                        if ($macaroni !== NULL)
                        {
                            $macaroni->delete();
                            /*if ($tempId !== NULL)
                            {
                                $delGr = TrainingGroupParticipantWork::find()->where(['id' => $tempId])->one();
                                if ($delGr !== NULL)
                                $delGr->delete();
                            }*/
                        }

                    }
                    /**/
                    Logger::WriteLog(Yii::$app->user->identity->getId(),
                        'От приказа '.$this->order_name . ' № ' . $this->order_number . '/' . $this->order_copy_id . (empty($this->order_postfix) ? '/' . $this->order_postfix : '') . ' откреплена учебная группа ' . $grName->number . ' и все её ученики');
                    $order->delete();
                }
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

        // тут новая таблица связка двух связок (паста)

        if ($this->participants_check[0] !== 'nope')
        {
            $groupsParticipantId = [];

            $groups = TrainingGroupParticipantWork::find();
            $ordersGroup = OrderGroupWork::find();
            $pastas = OrderGroupParticipantWork::find();

            if ($this->participants_check !== null)
            {
                for ($i = 0; $i < count($this->participants_check); $i++)
                {
                    $groupsParticipantId[] = $this->participants_check[$i];
                    $group = $groups->where(['id' => $this->participants_check[$i]])->one();

                    $orderGroup = $ordersGroup->where(['document_order_id' => $this->id])->andWhere(['training_group_id' => $group->training_group_id])->one();
                    $pasta = $pastas->where(['group_participant_id' => $this->participants_check[$i]])->andWhere(['order_group_id' => $orderGroup->id])->one();

                    $doubleEnrollmentCheck = count($pastas->where(['group_participant_id' => $this->participants_check[$i]])->andWhere(['status' => 0])->all());

                    //удаление явок

                    if ($doubleEnrollmentCheck > 0 && ($status == 1 || $status == 2))
                    {
                        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->training_group_id])->andWhere(['>=', 'lesson_date', $this->order_date])->all();

                        $lessonIds = [];
                        foreach ($lessons as $lesson) $lessonIds[] = $lesson->id;

                        $visits = VisitWork::find()->where(['foreign_event_participant_id' => $group->participant_id])->andWhere(['IN', 'training_group_lesson_id', $lessonIds])->all();
                        if (empty($visits))
                            Logger::WriteLog(Yii::$app->user->identity->getId(),
                                'Были удалены лишние явки у participant_id='.$group->participant_id.' в связи с несовпадением дат занятий и даты приказа '.$this->order_name . ' № ' . $this->order_number . '/' . $this->order_copy_id . (empty($this->order_postfix) ? '/' . $this->order_postfix : ''));

                        foreach ($visits as $visit)
                        {
                            $visit->status = 3;
                            $visit->save(false);
                        }
                    }

                    //-------------
                    if ($pasta === null)
                    {
                        $part = TrainingGroupParticipantWork::find()->where(['id' => $this->participants_check[$i]])->one();

                        if ($doubleEnrollmentCheck > 0 && $status == 0 || $doubleEnrollmentCheck == 0 && $status != 0)         // попытка двойного зачисления или отчислить/перевести не зачисленного
                            $flashMessage .= $part->participantWork->fullName . ', ';
                        else
                        {
                            $pasta = new OrderGroupParticipantWork();
                            $pasta->order_group_id = $orderGroup->id;
                            $pasta->group_participant_id = $this->participants_check[$i];
                            $pasta->status = $status;
                            $pasta->save();

                            // изменяем статус ученика
                            $group->status = $status;
                            $group->save();

                            Logger::WriteLog(Yii::$app->user->identity->getId(),
                                'Добавление part_id' . $this->participants_check[$i] . ' к приказу '. $this->order_name . ' № ' . $this->order_number . '/' . $this->order_copy_id . (empty($this->order_postfix) ? '/' . $this->order_postfix : ''));

                            // отдельная песня с переводом. если перевод, то это статус 2 (т.е. перевод) и новая запись со статусом 0 (т.е. зачисление)
                            // дополнительно тут же проверяем есть ли запись в связке первого уровня, и только после этого формируем пасту
                            if ($status == 2) {
                                $newGroup = $groups->where(['participant_id' => $group->participant_id])->andWhere(['training_group_id' => $this->new_groups_check[$this->participants_check[$i]][$group->participant_id][0]])->one();
                                if ($newGroup === null) {
                                    $trPr = new TrainingGroupParticipantWork();
                                    $trPr->participant_id = $group->participant_id;
                                    $trPr->training_group_id = $this->new_groups_check[$this->participants_check[$i]][$group->participant_id][0];
                                    $trPr->status = 0;
                                    $trPr->save();

                                    $trPr->addVisits($this->new_groups_check[$this->participants_check[$i]][$group->participant_id][0], $group->participant_id);
                                    $newGroup = $groups->where(['participant_id' => $group->participant_id])->andWhere(['training_group_id' => $this->new_groups_check[$this->participants_check[$i]][$group->participant_id][0]])->one();
                                }
                                $link = OrderGroupParticipantWork::find()->where(['order_group_id' => $orderGroup->id])->andWhere(['group_participant_id' => $this->participants_check[$i]])->andWhere(['status' => $status])->one();
                                $pasta = new OrderGroupParticipantWork();
                                $pasta->order_group_id = $orderGroup->id;
                                $pasta->group_participant_id = $newGroup->id;
                                $pasta->status = 0;
                                $pasta->link_id = $link->id;
                                $pasta->save();
                            }
                        }
                    }
                    else if ($status == 2)
                    {
                        $macaroni = OrderGroupParticipantWork::find()->where(['link_id' => $pasta->id])->one();
                        $newGroup = $groups->where(['participant_id' => $group->participant_id])->andWhere(['training_group_id' => $this->new_groups_check[$this->participants_check[$i]][$group->participant_id][0]])->one();
                        $macaroni->group_participant_id = $newGroup->id;
                        $macaroni->save();
                    }
                }
            }

            if ($this->participants_check !== null)
                $delParticipants = TrainingGroupParticipantWork::find()->where(['in', 'training_group_id', $groupsId])->andWhere(['not in', 'id', $groupsParticipantId])->all();
            else
                $delParticipants = TrainingGroupParticipantWork::find()->where(['in', 'training_group_id', $groupsId])->all();
            foreach ($delParticipants as $delParticipant)
            {
                $orderGroupParticipant = OrderGroupParticipantWork::find();
                $orderGroup = OrderGroupWork::find()->where(['document_order_id' => $this->id])->andWhere(['training_group_id' => $delParticipant->training_group_id])->one();
                $pasta = $orderGroupParticipant->where(['group_participant_id' => $delParticipant->id])->andWhere(['order_group_id' => $orderGroup->id])->one();

                if ($pasta->status == 2)    // перевод
                {
                    $defector = $orderGroupParticipant->where(['link_id' => $pasta->id])->one();
                    //var_dump($orderGroupParticipant->where(['link_id' => $pasta->id])->createCommand()->getRawSql());
                    $tempId = $defector->link_id;
                    $edit = $groups->where(['id' => $pasta->group_participant_id])->one();
                    $edit->status = 0;
                    $edit->save();
                    if ($defector !== NULL)
                        $defector->delete();
                    $delGr = $groups->where(['id' => $tempId])->one();
                    //var_dump($delGr->id);
                    /*if ($delGr !== NULL)
                        $delGr->delete();*/
                    //var_dump($delGr->getErrors());
                }

                if ($pasta->status === 1) // отчисление
                {
                    $edit = TrainingGroupParticipantWork::find()->where(['id' => $pasta->group_participant_id])->one();

                    if ($edit !== NULL)
                    {
                        $edit->status = 0;
                        $edit->save();
                    }
                }

                if ($pasta !== null)
                    $pasta->delete();

                if ($pasta->group_participant_id != '')
                    Logger::WriteLog(Yii::$app->user->identity->getId(),
                    'Открепление part_id ' . $pasta->group_participant_id . ' от приказа '. $this->order_name . ' № ' . $this->order_number . '/' . $this->order_copy_id . (empty($this->order_postfix) ? '/' . $this->order_postfix : ''));

            }
        }

        if (strlen($flashMessage) > 25)
        {
            $flashMessage = substr($flashMessage, 0, -2);
            if ($status == 0)
                $flashMessage .= ' невозможно - двойное зачисление запрещено';
            else
                $flashMessage .= ' невозможно - отчисление/перевод не зачисленных учащихся запрещено';
            Yii::$app->session->setFlash('danger', $flashMessage);
        }

        // если в группе была ошибка об отсутствии приказа, то тут она уйдет
        if ($this->groups_check !== null && count($this->groups_check) > 0)
        {
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckOrderTrainingGroup($this->groups_check);

        }

        if ($this->supplement !== null)
        {
            $docSup = DocumentOrderSupplement::find()->where(['document_order_id' => $this->id])->one();
            if ($docSup == null)
                $docSup = new DocumentOrderSupplementWork();

            $docSup->document_order_id = $this->id;
            $docSup->foreign_event_goals_id = $this->supplement['foreign_event_goals_id'];
            $docSup->compliance_document = $this->supplement['compliance_document'];
            $docSup->document_details = $this->supplement['document_details'];
            $docSup->information_deadline = $this->supplement['information_deadline'];
            $docSup->input_deadline = $this->supplement['input_deadline'];
            $docSup->collector_id = $this->supplement['collector_id'];
            $docSup->contributor_id = $this->supplement['contributor_id'];
            $docSup->methodologist_id = $this->supplement['methodologist_id'];
            $docSup->informant_id = $this->supplement['informant_id'];
            $docSup->save();
        }

        if ($this->foreign_event !== null)
        {
            $forEvent = ForeignEventWork::find()->where(['order_participation_id' => $this->id])->one();
            if ($forEvent == null)
                $forEvent = new ForeignEventWork();
            $forEvent->order_participation_id = $this->id;
            $forEvent->name = $this->foreign_event['name'];
            $forEvent->company_id = $this->foreign_event['company_id'];
            $forEvent->start_date = $this->foreign_event['start_date'];
            $forEvent->finish_date = $this->foreign_event['finish_date'];
            $forEvent->city = $this->foreign_event['city'];
            $forEvent->event_way_id = $this->foreign_event['event_way_id'];
            $forEvent->event_level_id = $this->foreign_event['event_level_id'];
            $forEvent->is_minpros = $this->foreign_event['is_minpros'];
            $forEvent->min_participants_age = $this->foreign_event['min_participants_age'];
            $forEvent->max_participants_age = $this->foreign_event['max_participants_age'];
            $forEvent->key_words = $this->foreign_event['key_words'];
            $forEvent->last_edit_id = Yii::$app->user->identity->getId();
            if ($forEvent->id == null)
            {
                if ($forEvent->creator_id === null)
                    $forEvent->creator_id = Yii::$app->user->identity->getId();
                $forEvent->business_trip = 0;
                $forEvent->order_business_trip_id = null;
                $forEvent->escort_id = null;
            }
            $forEvent->save();

            $this->uploadTeamName($forEvent->id);
            $this->uploadTeacherParticipants($forEvent->id);
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

        $orderGroup = OrderGroupWork::find()->where(['document_order_id' => $this->id])->all();
        foreach ($orderGroup as $order)
        {
            /**/ // удаление из связки
            $pasta = OrderGroupParticipantWork::find()->where(['order_group_id' => $order])->all();

            foreach ($pasta as $macaroni)
            {
                $tempId = NULL;

                if ($macaroni->status == 2)
                {
                    $defector = OrderGroupParticipantWork::find()->where(['link_id' => $macaroni->id])->one();
                    $tempId = $defector->group_participant_id;
                    $edit = TrainingGroupParticipantWork::find()->where(['id' => $macaroni->group_participant_id])->one();
                    if ($edit !== NULL)
                    {
                        $edit->status = 0;
                        $edit->save();
                    }
                    if ($defector !== NULL)
                        $defector->delete();
                }

                if ($macaroni->link_id !== NULL)
                {
                    $noDefector = OrderGroupParticipantWork::find()->where(['id' => $macaroni->link_id])->one();
                    $tempId = $macaroni->group_participant_id;
                    $edit = TrainingGroupParticipantWork::find()->where(['id' => $noDefector->group_participant_id])->one();
                    if ($edit !== NULL)
                    {
                        $edit->status = 0;
                        $edit->save();
                    }
                    if ($noDefector !== NULL)
                        $noDefector->delete();
                }

                if ($macaroni->status === 1) // отчисление
                {
                    $edit = TrainingGroupParticipantWork::find()->where(['id' => $macaroni->group_participant_id])->one();
                    if ($edit !== NULL)
                    {
                        $edit->status = 0;
                        $edit->save();
                    }
                }

                if ($macaroni !== NULL)
                {
                    $macaroni->delete();
                    /*if ($tempId !== NULL)
                    {
                        $delGr = TrainingGroupParticipantWork::find()->where(['id' => $tempId])->one();
                        if ($delGr !== NULL)
                            $delGr->delete();
                    }*/
                }
            }
            /**/

            $order->delete();
        }


        if ($this->groups_check !== null && count($this->groups_check) > 0)
        {
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckOrderTrainingGroup($this->groups_check);
        }

        $errors = OrderErrorsWork::find()->where(['document_order_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();

        $supplement = DocumentOrderSupplementWork::find()->where(['document_order_id' => $this->id])->one();
        if ($supplement != null) $supplement->delete();

        $foreingEvent = ForeignEventWork::find()->where(['order_participation_id' => $this->id])->one();
        if ($foreingEvent != null) $foreingEvent->delete();

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

    public function getForeignEventLink()
    {
        $foreigns = ForeignEventWork::find()->where(['order_participation_id' => $this->id])->all();
        $result = '';

        foreach ($foreigns as $foreign)
        {
            $result .= Html::a($foreign->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $foreign->id]));
            if ($foreign->getErrorsWork() !== '')
                $result .= '<span style="color:red"> (содержит ошибки)</span>';
            $result .= '<br>';
        }

        return $result;
    }
}