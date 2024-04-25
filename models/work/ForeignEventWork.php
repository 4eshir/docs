<?php

namespace app\models\work;

use app\models\common\Company;
use app\models\common\DocumentOrder;
use app\models\common\EventLevel;
use app\models\common\EventWay;
use app\models\common\ForeignEvent;
use app\models\common\ParticipantAchievement;
use app\models\common\ParticipantFiles;
use app\models\common\People;
use app\models\common\TeacherParticipant;
use app\models\common\Team;
use app\models\common\User;
use app\models\components\FileWizard;
use app\models\null\CompanyNull;
use app\models\null\EventLevelNull;
use Yii;
use yii\helpers\Html;


class ForeignEventWork extends ForeignEvent
{
    public $participants;
    public $achievement;
    public $team;

    public $docsAchievement;

    public function rules()
    {
        return [
            [['name', 'company_id', 'start_date', 'finish_date', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'key_words'], 'required'],
            [['company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id', 'copy', 'is_minpros', 'add_order_participation_id', 'last_edit_id'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['name', 'city', 'key_words', 'docs_achievement', 'companyString', 'participants'], 'string', 'max' => 1000],
            [['docs_achievement'], 'file', 'extensions' => 'jpg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['event_way_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventWay::className(), 'targetAttribute' => ['event_way_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevel::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['order_participation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_participation_id' => 'id']],
            [['order_business_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_business_trip_id' => 'id']],
            [['add_order_participation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['add_order_participation_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'company_id' => 'Организатор',
            'companyString' => 'Организатор',
            'start_date' => 'Дата начала',
            'finish_date' => 'Дата окончания',
            'city' => 'Город',
            'event_way_id' => 'Формат проведения',
            'eventWayString' => 'Формат проведения',
            'event_level_id' => 'Уровень',
            'eventLevelString' => 'Уровень',
            'ageRange' => 'Возраст участников',
            'min_participants_age' => 'Мин. возраст участников (лет)',
            'max_participants_age' => 'Макс. возраст участников (лет)',
            'business_trip' => 'Командировка',
            'businessTrip' => 'Командировка',
            'escort_id' => 'Сопровождающий',
            'order_participation_id' => 'Приказ об участии',
            'add_order_participation_id' => 'Дополнительный приказ об участии',
            'orderParticipationString' => 'Приказ об участии',
            'addOrderParticipationString' => 'Дополнительный приказ',
            'order_business_trip_id' => 'Приказ о направлении (командировка)',
            'orderBusinessTripString' => 'Приказ о направлении (командировка)',
            'key_words' => 'Ключевые слова',
            'docs_achievement' => 'Документы о достижениях',
            'participantsLink' => 'Участники',
            'achievementsLink' => 'Достижения',
            'docString' => 'Документ о достижениях',
            'docsAchievement' => 'Документ о достижениях',
            'teachers' => 'Педагоги',
            'teachersExport' => 'Педагоги',
            'winners' => 'Победители',
            'prizes' => 'Призеры',
            'businessTrips' => 'Командировка',
            'participantCount' => 'Кол-во участников',
            'participants' => 'Участники',
            'creatorString' => 'Создатель карточки',
            'is_minpros' => 'Входит в перечень Минпросвещения РФ',
            'isMinpros' => 'Входит в перечень Минпросвещения РФ',
            'last_edit_id' => 'Последний редактор карточки',
            'editorString' => 'Последний редактор карточки',
        ];
    }

    public function getCompanyWork()
    {
        $try = $this->hasOne(CompanyWork::className(), ['id' => 'company_id']);
        return $try->all() ? $try : new CompanyNull();
    }

    public function getEventLevelWork()
    {
        $try = $this->hasOne(EventLevelWork::className(), ['id' => 'event_level_id']);
        return $try->all() ? $try : new EventLevelNull();
    }

    public function getIsMinpros()
    {
        return $this->is_minpros == 0 ? 'Нет' : 'Да';
    }

    public function getCompanyString()
    {
        $company = CompanyWork::find()->where(['id' => $this->company_id])->one();
        return $company->name;
    }

    public function getEventWayString()
    {
        return $this->eventWay->name;
    }

    public function getEventLevelString()
    {
        return $this->eventLevel->name;
    }

    public function getAgeRange()
    {
        return $this->min_participants_age.' - '.$this->max_participants_age.' (лет)';
    }

    public function getBusinessTrip()
    {
        return $this->business_trip == 0 ? 'Нет' : 'Да';
    }

    /**
     * Gets query for [[EventLevel]].
     *
     * @return string
     */


    public function getColor($participant_id, $partOne_id)
    {
        $branchEvent = [];
        $branchTrG = [];

        $branchSet = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $partOne_id])->all();
        foreach ($branchSet as $branch)
            $branchEvent[] = $branch->branch_id;

        $trG = TrainingGroupWork::find()->joinWith(['trainingGroupParticipants trainingGroupParticipants'])->where(['trainingGroupParticipants.participant_id' => $participant_id])->all();
        foreach ($trG as $group)
            $branchTrG[] = $group->branch_id;

        if (count(array_intersect($branchEvent, $branchTrG)) === 0)
            return 'style = "background-color: #FCF8E3; margin: 0;"';

        return 'style = "margin: 0;"';
    }

    public function getParticipantsLink()
    {
        $parts = TeacherParticipantWork::find()->where(['foreign_event_id' => $this->id])->all();
        $partsLink = '';
        $branchSet =  BranchWork::find();
        
        foreach ($parts as $partOne)
        {
            $branchs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $partOne->id])->all();
            $branchsId = [];
            foreach ($branchs as $branch) $branchsId[] = $branch->branch_id;
            $partsLink .= '<p ' . $this->getColor($partOne->participant_id, $partOne->id) . '>';
            $team = TeamWork::find()->where(['teacher_participant_id' => $partOne->id])->one();
            $partsLink = $partsLink.Html::a($partOne->participantWork->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $partOne->participant_id])).' (педагог(-и): '.Html::a($partOne->teacherWork->shortName, \yii\helpers\Url::to(['people/view', 'id' => $partOne->teacher_id]));
            if ($partOne->teacher2_id !== null) $partsLink .= ' '.Html::a($partOne->teacher2Work->shortName, \yii\helpers\Url::to(['people/view', 'id' => $partOne->teacher2_id]));
            $branchs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $partOne->id])->all();
            $tempStr = '';
            foreach ($branchs as $branch)
                $tempStr .= Html::a($branch->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $branch->branch_id])).', ';
             $tempStr = substr($tempStr, 0, -2);

            $partsLink .= ', отдел(-ы) для учета: ' . $tempStr;
            $partsLink .= ')';
            if ($team != null)
                $partsLink = $partsLink.' - Команда '.$team->teamNameWork->name;
            $partsLink .= '</p>';
        }
        return $partsLink;
    }

    public function getAchievementsLink()
    {
        $parts = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $this->id])->orderBy(['winner' => SORT_DESC])->all();
        $partsLink = '';
        foreach ($parts as $partOne)
        {
            $team = TeamWork::find()->where(['teacher_participant_id' => $partOne->teacherParticipantWork->id])->one();
            $tpb = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $partOne->teacherParticipantWork->id])->all();
            $branchStr = '';
            $teamStr = '[индивидуальное участие]';
            if ($team != null)
                $teamStr = '['.$team->teamNameWork->name.']';
            foreach ($tpb as $one)
                $branchStr .= $one->branch->name.' | ';

            $branchStr = '[' . substr($branchStr, 0, -3) . ']';

            $value = $partOne->winner == 1 ? 'Победитель: ' : 'Призер: ';

            $partsLink = $partsLink. $value .Html::a($partOne->teacherParticipantWork->participantWork->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $partOne->teacherParticipantWork->participant_id])).' '.$branchStr.' '.$teamStr.' &mdash; '.$partOne->achievment.'<br>';
        }
        return $partsLink;
    }

    public function getDocumentOrderWork()
    {
        return DocumentOrderWork::find()->where(['id' => $this->order_participation_id])->one();
    }

    public function getOrderParticipationString()
    {
        $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $this->order_participation_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getAddOrderParticipationString()
    {
        $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $this->add_order_participation_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getOrderBusinessTripString()
    {
        $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $this->order_business_trip_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getDocString()
    {
        return Html::a($this->docs_achievement, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $this->docs_achievement, 'type' => 'achievements_files']));
    }

    public function getEscort()
    {
        return PeopleWork::find()->where(['id' => $this->escort_id])->one();
    }

    public function getParticipantCount()
    {
        $result = '';
        $teachPart = TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all();
        $allPart = [];
        foreach ($teachPart as $tP)
            $allPart[] = $tP->id;

        $result .= count($teachPart);
        $tech = TeacherParticipantBranchWork::find()->where(['branch_id' => 2])->andWhere(['IN', 'teacher_participant_id', $allPart])->all();
        $kvant =  TeacherParticipantBranchWork::find()->where(['branch_id' => 1])->andWhere(['IN', 'teacher_participant_id', $allPart])->all();
        $cdntt =  TeacherParticipantBranchWork::find()->where(['branch_id' => 3])->andWhere(['IN', 'teacher_participant_id', $allPart])->all();
        $mobKvant =  TeacherParticipantBranchWork::find()->where(['branch_id' => 4])->andWhere(['IN', 'teacher_participant_id', $allPart])->all();
        $cod =  TeacherParticipantBranchWork::find()->where(['branch_id' => 7])->andWhere(['IN', 'teacher_participant_id', $allPart])->all();

        if ($tech != null || $kvant != null || $cdntt != null || $mobKvant != null || $cod != null)
            $result .= '<br> ( ';
        if ($tech != null)
            $result .= count($tech) . ' - из отдела Технопарк; <br>';
        if ($kvant != null)
            $result .= count($kvant) . ' - из отдела Кванториум; <br>';
        if ($cdntt != null)
            $result .= count($cdntt) . ' - из отдела ЦДНТТ; <br>';
        if ($mobKvant != null)
            $result .= count($mobKvant) . ' - из отдела Мобильный кванториум; <br>';
        if ($cod != null)
            $result .= count($cod) . ' - из отдела ЦОД; <br>';
        $result = substr($result, 0, -6);
        if ($tech != null || $kvant != null || $cdntt != null || $mobKvant != null || $cod != null)
            $result .= ')';

        return $result;
    }

    public function getTeachers()
    {
        $teachers = TeacherParticipantWork::find()->select(['teacher_id'])->where(['foreign_event_id' => $this->id])->distinct()->all();
        $teacherList = '';
        foreach ($teachers as $teacherOne)
        {
            $teacherList = $teacherList.$teacherOne->teacherWork->shortName.'<br>';
        }
        return $teacherList;
    }

    public function getTeachersExport()
    {
        $teachers = TeacherParticipantWork::find()->select(['teacher_id'])->where(['foreign_event_id' => $this->id])->distinct()->all();
        $teacherList = '';
        foreach ($teachers as $teacherOne)
        {
            $teacherList = $teacherList.$teacherOne->teacherWork->shortName.' ';
        }
        return $teacherList;
    }

    public function getWinners()
    {
        $parts = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $this->id])->andWhere(['winner' => 1])->all();
        $partsList = '';
        foreach ($parts as $partOne)
        {
            $team = TeamWork::find()->where(['teacher_participant_id' => $partOne->teacher_participant_id])->one();
            if ($team !== null)
                $partsList = $partsList.$partOne->teacherParticipantWork->participantWork->shortName.' ('.$team->teamNameWork->name.')<br>';
            else
                $partsList = $partsList.$partOne->teacherParticipantWork->participantWork->shortName.'<br>';
        }
        return $partsList;
    }

    public function getPrizes()
    {
        $parts = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $this->id])->andWhere(['winner' => 0])->all();
        $partsList = '';
        foreach ($parts as $partOne)
        {
            $partsList = $partsList.$partOne->teacherParticipantWork->participantWork->shortName.'<br>';
        }
        return $partsList;
    }

    public function getBusinessTrips()
    {
        return $this->business_trip == 1 ? 'Да' : 'Нет';
    }

    public function getErrorsWork()
    {
        $errorsList = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $error = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            $result .= 'Внимание, ошибка: ' . $error->number . ' ' . $error->name . '<br>';
        }
        return $result;
    }

    public function GetCreatorString()
    {
        $user = UserWork::find()->where(['id' => $this->creator_id])->one();
        return $user->fullName;
    }

    public function GetEditorString()
    {
        $user = UserWork::find()->where(['id' => $this->last_edit_id])->one();
        return $user->fullName;
    }

    public function uploadAchievementsFile()
    {
        $path = '@app/upload/files/foreign-event/achievements_files/';
        $date = $this->start_date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = '';
        $filename = 'Д.'.$new_date.'_'.$this->name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^a-zA-Zа-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->docs_achievement = $res . '.' . $this->docsAchievement->extension;
        $this->docsAchievement->saveAs( $path . $res . '.' . $this->docsAchievement->extension);
    }

    public function beforeSave($insert)
    {
        $this->last_edit_id = Yii::$app->user->identity->getId();

        if ($this->creator_id === null)
            $this->creator_id = Yii::$app->user->identity->getId();

        if ($this->business_trip == 0)
        {
            $this->order_business_trip_id = null;
            $this->escort_id = null;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->uploadTeacherParticipants();
        $this->uploadParticipantFiles();
        $this->uploadParticipantAchievement();
        $this->uploadParticipantTeam();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        // тут должны работать проверки на ошибки
        $errorsCheck = new ForeignEventErrorsWork();
        $errorsCheck->CheckErrorsForeignEventWithoutAmnesty($this->id);
    }

    private function uploadParticipantTeam()
    {
        if ($this->participants !== null)
        {
            foreach ($this->participants as $partOne)
            {
                if (strlen($partOne->team) > 0)
                {
                    $result = str_replace('Команда ', '', $partOne->team);
                    $result = str_replace('команда ', '', $result);
                    $partOne->team = $result;
                    $part = new Team();
                    $part->foreign_event_id = $this->id;
                    $part->participant_id = $partOne->fio;
                    $part->name = $partOne->team;
                    $part->save();
                }
            }
        }
    }

    private function uploadTeacherParticipants()
    {
        $str = '';
        if ($this->participants !== null)
        {
            foreach ($this->participants as $participantOne)
            {
                //$duplicate = TeacherParticipantWork::find()->where(['participant_id' => $participantOne->fio])->andWhere(['foreign_event_id' => $this->id])->all();
                if (true)//(count($duplicate) == 0)
                {
                    $part = new TeacherParticipantWork();
                    $part->foreign_event_id = $this->id;
                    $part->participant_id = $participantOne->fio;
                    $part->teacher_id = $participantOne->teacher;
                    $part->teacher2_id = $participantOne->teacher2;
                    $part->focus = $participantOne->focus;
                    $part->allow_remote_id = $participantOne->allow_remote_id;
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
                }
                else
                    $str .= 'Попытка добавления дубликата.<br>';
            }
            if ($str != '')
                Yii::$app->session->setFlash('danger', $str);
        }
    }

    private function uploadParticipantFiles()
    {
        if ($this->participants)
        {
            foreach ($this->participants as $participantOne)
            {
                $part = new ParticipantFiles();
                $part->foreign_event_id = $this->id;
                $part->participant_id = $participantOne->fio;
                $part->filename = $participantOne->fileString;
                $part->save();
            }
        }
    }

    private function uploadParticipantAchievement()
    {
        $str = '';
        if ($this->achievement)
        {
            foreach ($this->achievement as $achievementOne)
            {
                $duplicate = ParticipantAchievementWork::find()->where(['teacher_participant_id' => $achievementOne->fio])->one();

                if ($duplicate == null)
                {
                    $team = TeamWork::find()->where(['teacher_participant_id' => $achievementOne->fio])->one();

                    $part = new ParticipantAchievement();
                    $part->teacher_participant_id = $achievementOne->fio;
                    $part->achievment = $achievementOne->achieve;
                    $part->winner = $achievementOne->winner;
                    if ($achievementOne->cert_number != '')
                    {
                        $part->cert_number = $achievementOne->cert_number;
                        $part->date = $achievementOne->date;
                    }
                    $part->team_name_id = $team->team_name_id;
                    $part->save();

                    if ($team->team_name_id != null)
                    {
                        $teamParts = TeamWork::find()->where(['team_name_id' => $team->team_name_id])->andWhere(['!=', 'teacher_participant_id', $achievementOne->fio])->all();
                        foreach ($teamParts as $onePart)
                        {
                            $part = new ParticipantAchievement();
                            $part->teacher_participant_id = $onePart->teacher_participant_id;
                            $part->achievment = $achievementOne->achieve;
                            $part->winner = $achievementOne->winner;
                            if ($achievementOne->cert_number != '')
                            {
                                $part->cert_number = $achievementOne->cert_number;
                                $part->date = $achievementOne->date;
                            }
                            $part->team_name_id = $team->team_name_id;
                            $part->save();
                        }
                    }
                }
                else
                    $str .= 'Попытка добавления дубликата.<br>';
                
            }
            if ($str != '')
                Yii::$app->session->setFlash('danger', $str);
        }
    }

    public function beforeDelete()
    {
        $this->deleteParticipantAchievement();
        $this->deleteTeam();
        $this->deleteTeacherParticipantBranch();
        $this->deleteTeacherParticipant();
        $this->deleteParticipantFile();

        $errors = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    private function deleteTeacherParticipantBranch()
    {
        $tp = TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all();
        $tbIds = [];
        foreach ($tp as $one) $tbIds[] = $one->id;
        $tp = TeacherParticipantBranchWork::find()->where(['IN', 'teacher_participant_id', $tbIds])->all();
        foreach ($tp as $tpOne) { $tpOne->delete(); }
    }

    private function deleteTeam()
    {
        $teamName = TeamNameWork::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($teamName as $one) $one->delete();

        $tp = TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all();
        $tbIds = [];
        foreach ($tp as $one) $tbIds[] = $one->id;
        $teams = TeamWork::find()->where(['IN', 'teacher_participant_id', $tbIds])->all();
        foreach ($teams as $one) $one->delete();
    }

    private function deleteTeacherParticipant()
    {
        $tp = TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($tp as $tpOne) { $tpOne->delete(); }
    }

    private function deleteParticipantAchievement()
    {
        $pa = ParticipantAchievement::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($pa as $paOne) { $paOne->delete(); }
    }

    private function deleteParticipantFile()
    {
        $pf = ParticipantFiles::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($pf as $pfOne) { $pfOne->delete(); }
    }
}
