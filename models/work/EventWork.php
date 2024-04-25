<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\Event;
use app\models\common\EventBranch;
use app\models\common\EventExternal;
use app\models\common\EventForm;
use app\models\common\EventLevel;
use app\models\common\EventParticipants;
use app\models\common\EventScope;
use app\models\common\EventsLink;
use app\models\common\EventType;
use app\models\common\ForeignEvent;
use app\models\common\People;
use app\models\common\Regulation;
use app\models\components\FileWizard;
use app\models\null\DocumentOrderNull;
use app\models\null\ParticipationScopeNull;
use app\models\null\PeopleNull;
use Yii;
use yii\helpers\Html;


class EventWork extends Event
{
    public $protocolFile;
    public $photoFiles;
    public $reportingFile;
    public $otherFiles;
    public $eventsLink;
    public $groups;
    public $scopes;

    public $isTechnopark;
    public $isQuantorium;
    public $isCDNTT;
    public $isMobQuant;
    public $isCod;

    public $yesEducation;
    public $noEducation;

    public $childs;
    public $childs_rst;
    public $teachers;
    public $others;
    public $leftAge;
    public $rightAge;
    public $eventLevelString;


    //--Конструктор для тестов--
    function __construct($tId = null, $tName = null, $tEventTypeId = null, $tEventFormId = null, $tEventLevelId = null, $tFinishDate = null)
    {
        if ($tId == null)
            return;

        $this->id = $tId;
        $this->name = $tName;
        $this->event_type_id = $tEventTypeId;
        $this->event_form_id = $tEventFormId;
        $this->event_level_id = $tEventLevelId;
        $this->finish_date = $tFinishDate;

        //--Дефолтные значения--
        $this->old_name = 'DEFAULT';
        $this->start_date = '1999-01-01';
        $this->address = 'DEFAULT';
        $this->participants_count = 0;
        $this->is_federal = 0;
        $this->responsible_id = 0;
        $this->responsible2_id = 0;
        $this->key_words = 'DEFAULT';
        $this->comment = 'DEFAULT';
        $this->order_id = 0;
        $this->regulation_id = 0;
        $this->protocol = 'DEFAULT';
        $this->photos = 'DEFAULT';
        $this->reporting_doc = 'DEFAULT';
        $this->other_files = 'DEFAULT';
        $this->contains_education = 0;
        $this->event_way_id = 0;
        $this->creator_id = 0;
        $this->last_edit_id = 0;
        $this->participation_scope_id = 0;
        //----------------------
    }
    //--------------------------


    public function rules()
    {
        return [
            [['name', 'start_date', 'finish_date', 'event_type_id', 'event_form_id', 'address', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'protocol', 'contains_education', 'event_way_id'], 'required'],
            [['start_date', 'finish_date', 'scopes'], 'safe'],
            [['responsibleString', 'eventLevelString'], 'string'],
            [['format', 'event_type_id', 'event_form_id', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'isTechnopark', 'isQuantorium', 'isCDNTT', 'isMobQuant','isCod', 'contains_education', 'childs', 'teachers', 'others', 'leftAge', 'rightAge', 'childs_rst', 'participation_scope_id'], 'integer'],
            [['address', 'key_words', 'comment', 'protocol', 'photos', 'reporting_doc', 'other_files', 'name', 'old_name'], 'string', 'max' => 1000],
            [['participation_scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => ParticipationScopeWork::className(), 'targetAttribute' => ['participation_scope_id' => 'id']],
            [['event_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventFormWork::className(), 'targetAttribute' => ['event_form_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevelWork::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['event_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventTypeWork::className(), 'targetAttribute' => ['event_type_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrderWork::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => RegulationWork::className(), 'targetAttribute' => ['regulation_id' => 'id']],
            [['responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleWork::className(), 'targetAttribute' => ['responsible_id' => 'id']],
            [['responsible2_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleWork::className(), 'targetAttribute' => ['responsible_id' => 'id']],
            [['protocolFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['photoFiles'], 'file', 'extensions' => 'jpg, png, jpeg, gi, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['reportingFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['otherFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Дата начала',
            'finish_date' => 'Дата окончания',
            'event_type_id' => 'Тип мероприятия',
            'event_form_id' => 'Форма мероприятия',
            'eventWayString' => 'Формат проведения',
            'address' => 'Адрес проведения',
            'event_level_id' => 'Уровень мероприятия',
            'participants_count' => 'Кол-во участников',
            'is_federal' => 'Входит в ФП',
            'responsible_id' => 'Ответственный(-ые) работник(-и)',
            'responsible2_id' => 'Ответственный работник',
            'key_words' => 'Ключевые слова',
            'comment' => 'Примечание',
            'order_id' => 'Приказ',
            'regulation_id' => 'Положение',
            'protocol' => 'Протоколы',
            'photos' => 'Фотоматериалы',
            'reporting_doc' => 'Явочный документ',
            'other_files' => 'Другие файлы',
            'protocolFile' => 'Протокол мероприятия',
            'reportingFile' => 'Явочные документы',
            'photoFiles' => 'Фотоматериалы',
            'otherFiles' => 'Другие файлы',
            'name' => 'Название мероприятия',
            'isTechnopark' => 'Технопарк',
            'isQuantorium' => 'Кванториум',
            'isMobQuant' => 'Мобильный кванториум',
            'isCDNTT' => 'ЦДНТТ',
            'isCod' => 'Центр одаренных детей',
            'contains_education' => 'Содержит образовательные программы',
            'yesEducation' => 'Содержит образовательные программы',
            'noEducation' => 'Не содержит образовательные программы',
            'childs' => 'Кол-во детей',
            'childs_rst' => 'В т.ч. обучающихся РШТ',
            'teachers' => 'Кол-во педагогов',
            'others' => 'Кол-во иных',
            'leftAge' => 'Возраст детей: минимальный, лет',
            'rightAge' => 'Возраст детей: максимальный, лет',
            'formatString' => 'Формат проведения',
            'creatorString' => 'Создатель карточки',
            'editorString' => 'Редактор карточки',
            'linkGroups' => 'Связанные группы',
            'scopesString' => 'Сферы участия',
        ];
    }

    public function getFormatString()
    {
        if ($this->format === 0) return 'Очный';
        if ($this->format === 1) return 'Заочный';
        if ($this->format === 2) return 'Очно-заочный';
    }

    public function getScopesSplitter()
    {
        $ess = EventScopeWork::find()->where(['event_id' => $this->id])->all();
        $res = '';
        foreach ($ess as $one)
        {
            $res .= $one->participationScope->name.'/';
        }
        $res = substr($res, 0, -1);
        return $res;
    }

    public function getLinkGroups()
    {
        $result = '';
        $groups = EventTrainingGroupWork::find()->where(['event_id' => $this->id])->all();
        foreach ($groups as $group)
            $result .= Html::a($group->trainingGroupWork->numberExtended, \yii\helpers\Url::to(['training-group/view', 'id' => $group->training_group_id])) . '<br>';

        return $result;
    }

    public function getScopesString()
    {
        $ess = EventScopeWork::find()->where(['event_id' => $this->id])->all();
        $res = '';
        foreach ($ess as $one)
        {
            $res .= $one->participationScope->name.'<br>';
        }
        return $res;
    }

    public function getChildsString()
    {
        $parts = EventParticipantsWork::find()->where(['event_id' => $this->id])->one();
        return $parts->childs;
    }

    public function getChildsRstString()
    {
        $parts = EventParticipantsWork::find()->where(['event_id' => $this->id])->one();
        return $parts->childs_rst;
    }

    public function getTeachersString()
    {
        $parts = EventParticipantsWork::find()->where(['event_id' => $this->id])->one();
        return $parts->teachers;
    }

    public function getOthersString()
    {
        $parts = EventParticipantsWork::find()->where(['event_id' => $this->id])->one();
        return $parts->others;
    }

    public function getEventTypeString()
    {
        return EventTypeWork::find()->where(['id' => $this->event_type_id])->one()->name;
    }


    public function getResponsibleWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'responsible_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getParticipationScopeWork()
    {
        $try = $this->hasOne(ParticipationScopeWork::className(), ['id' => 'participation_scope_id']);
        return $try->all() ? $try : new ParticipationScopeNull();
    }

    public function getResponsibleWork2()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'responsible2_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getEventBranchs()
    {
        $branchs = EventBranch::find()->where(['event_id' => $this->id])->all();
        $result = '';
        foreach ($branchs as $branch)
        {
            $result .= $branch->branch->name.'<br>';
        }
        return $result;
    }

    public function getOrderWork()
    {
        if ($this->order_id != null)
        {
            $try = $this->hasOne(DocumentOrderWork::className(), ['id' => 'order_id']);
            return $try->all() ? $try : new DocumentOrderNull();
        }
    }

    public function getResponsibleString()
    {
        return PeopleWork::find()->where(['id' => $this->responsible_id])->one()->shortName;
    }

    public function getEventWayString()
    {
        $way = EventWayWork::find()->where(['id' => $this->event_way_id])->one();
        return $way->name;
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

    //---------------------------------

    public function uploadProtocolFile($upd = null)
    {
        $path = '@app/upload/files/event/protocol/';
        $counter = 0;
        if (strlen($this->protocol) > 3)
            $counter = count(explode(" ", $this->protocol)) - 1;
        foreach ($this->protocolFile as $file) {
            $counter++;
            $date = $this->order->order_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->order->order_postfix == null)
                $filename = 'Пр'.$counter.'_'.$new_date.'_'.$this->start_date.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Пр'.$counter.'_'.$new_date.'_'.$this->start_date.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            $filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->protocol = $result;
        else
            $this->protocol = $this->protocol.$result;
        return true;
    }

    public function uploadReportingFile($upd = null)
    {
        $path = '@app/upload/files/event/reporting/';

        $counter = 0;
        if (strlen($this->reporting_doc) > 3)
            $counter = count(explode(" ", $this->reporting_doc)) - 1;
        foreach ($this->reportingFile as $file) {
            $counter++;
            $date = $this->order->order_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->order->order_postfix == null)
                $filename = 'Яв'.$counter.'_'.$new_date.'_'.$this->start_date.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Яв'.$counter.'_'.$new_date.'_'.$this->start_date.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            //$filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $res = FileWizard::CutFilename($res).'_'.$this->getEventNumber();
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->reporting_doc = $result;
        else
            $this->reporting_doc = $this->reporting_doc.$result;
        return true;
    }

    public function uploadPhotosFiles($upd = null)
    {
        $path = '@app/upload/files/event/photos/';
        $result = '';
        $counter = 0;
        if (strlen($this->photos) > 3)
            $counter = count(explode(" ", $this->photos)) - 1;
        foreach ($this->photoFiles as $file) {
            $counter++;
            $date = $this->order->order_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->order->order_postfix == null)
                $filename = 'Фото'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Фото'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            //$filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $res = FileWizard::CutFilename($res).'_'.$this->getEventNumber();
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->photos = $result;
        else
            $this->photos = $this->photos.$result;
        return true;
    }

    public function uploadOtherFiles($upd = null)
    {
        $path = '@app/upload/files/event/other/';
        $result = '';
        $counter = 0;
        if (strlen($this->other_files) > 3)
            $counter = count(explode(" ", $this->other_files)) - 1;
        foreach ($this->otherFiles as $file) {
            $counter++;
            $date = $this->order->order_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->order->order_postfix == null)
                $filename = 'Файл'.$counter.'_'.$new_date.'_'.$this->start_date.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Файл'.$counter.'_'.$new_date.'_'.$this->start_date.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            //$filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res).'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->other_files = $result;
        else
            $this->other_files = $this->other_files.$result;
        return true;
    }

    public function getEventNumber()
    {
        if ($this->id !== null)
            return $this->id;
        $events = Event::find()->orderBy(['id' => SORT_DESC])->all();
        return $events[0]->id + 1;
    }

    //------------------------

    public function beforeDelete()
    {
        $eb = EventBranch::find()->where(['event_id' => $this->id])->all();
        foreach ($eb as $ebOne)
            $ebOne->delete();

        $errors = EventErrorsWork::find()->where(['event_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();

        $ec = EventScopeWork::find()->where(['event_id' => $this->id])->all();
        foreach ($ec as $ecOne) $ecOne->delete();

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id === null)
            $this->creator_id = Yii::$app->user->identity->getId();
        $this->last_edit_id = Yii::$app->user->identity->getId();
        $this->participants_count = $this->childs + $this->teachers + $this->others;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub


        if ($this->scopes !== null && $this->scopes !== "")
        {
            foreach ($this->scopes as $scope)
            {
                $es = EventScopeWork::find()->where(['event_id' => $this->id])->andWhere(['participation_scope_id' => $scope])->one();
                if ($es == null) $es = new EventScopeWork();
                $es->event_id = $this->id;
                $es->participation_scope_id = $scope;
                $es->save();
            }
        }

        if ($this->eventsLink !== null)
        {
            foreach ($this->eventsLink as $eventLink)
            {
                if ($eventLink->eventExternalName !== '')
                {
                    $evnLnk = new EventsLink();
                    $evnLnk->event_id = $this->id;
                    $evnLnk->event_external_id = EventExternal::find()->where(['id' => $eventLink->eventExternalName])->one()->id;
                    $evnLnk->save(false);
                }

            }
        }

        if ($this->eventsLink !== null)
        {
            foreach ($this->eventsLink as $eventLink)
            {
                if ($eventLink->eventExternalName !== '')
                {
                    $evnLnk = new EventsLink();
                    $evnLnk->event_id = $this->id;
                    $evnLnk->event_external_id = EventExternal::find()->where(['id' => $eventLink->eventExternalName])->one()->id;
                    $evnLnk->save(false);
                }

            }
        }

        if ($this->groups !== null)
        {
            foreach ($this->groups as $group)
            {
                if ($group->training_group_id !== '')
                {
                    $newGroup = new EventTrainingGroupWork();
                    $newGroup->training_group_id = $group->training_group_id;
                    $newGroup->event_id = $this->id;
                    $newGroup->save();
                }

            }
        }

        $edT = new EventBranch();
        if ($this->isTechnopark == 1)
        {
            $edT->branch_id = 2;
            $edT->event_id = $this->id;
            if (count(EventBranch::find()->where(['branch_id' => 2])->andWhere(['event_id' => $this->id])->all()) == 0)
                $edT->save();
        }
        else
        {
            $edT = EventBranch::find()->where(['branch_id' => 2])->andWhere(['event_id' => $this->id])->one();
            if ($edT !== null)
                $edT->delete();
        }

        $edQ = new EventBranch();
        if ($this->isQuantorium == 1)
        {
            $edQ->branch_id = 1;
            $edQ->event_id = $this->id;
            if (count(EventBranch::find()->where(['branch_id' => 1])->andWhere(['event_id' => $this->id])->all()) == 0)
                $edQ->save();
        }
        else
        {
            $edQ = EventBranch::find()->where(['branch_id' => 1])->andWhere(['event_id' => $this->id])->one();
            if ($edQ !== null)
                $edQ->delete();
        }

        $edC = new EventBranch();
        if ($this->isCDNTT == 1)
        {
            $edC->branch_id = 3;
            $edC->event_id = $this->id;
            if (count(EventBranch::find()->where(['branch_id' => 3])->andWhere(['event_id' => $this->id])->all()) == 0)
                $edC->save();
        }
        else
        {
            $edC = EventBranch::find()->where(['branch_id' => 3])->andWhere(['event_id' => $this->id])->one();
            if ($edC !== null)
                $edC->delete();
        }

        $edM = new EventBranch();
        if ($this->isMobQuant == 1)
        {
            $edM->branch_id = 4;
            $edM->event_id = $this->id;
            if (count(EventBranch::find()->where(['branch_id' => 4])->andWhere(['event_id' => $this->id])->all()) == 0)
                $edM->save();
        }
        else
        {
            $edM = EventBranch::find()->where(['branch_id' => 4])->andWhere(['event_id' => $this->id])->one();
            if ($edM !== null)
                $edM->delete();
        }

        $edCod = new EventBranch();
        if ($this->isCod == 1)
        {
            $edCod->branch_id = 7;
            $edCod->event_id = $this->id;
            if (count(EventBranch::find()->where(['branch_id' => 7])->andWhere(['event_id' => $this->id])->all()) == 0)
                $edCod->save();
        }
        else
        {
            $edCod = EventBranch::find()->where(['branch_id' => 7])->andWhere(['event_id' => $this->id])->one();
            if ($edCod !== null)
                $edCod->delete();
        }

        $eventP = EventParticipants::find()->where(['event_id' => $this->id])->one();
        if ($eventP == null)
            $eventP = new EventParticipants();
        $eventP->child_participants = $this->childs;
        $eventP->child_rst_participants = $this->childs_rst;
        $eventP->teacher_participants = $this->teachers;
        $eventP->other_participants = $this->others;
        $eventP->age_left_border = $this->leftAge;
        $eventP->age_right_border = $this->rightAge;
        $eventP->event_id = $this->id;
        $eventP->save();

        /* В связи с изменением в учете достижений - связка мероприятия и учета достижения не создается. Все достижения - через приказ
         * if ($this->eventType->name == 'Соревновательный' && $insert) {
            $this->copyEvent();
        }
        else if ($this->eventType->name == 'Соревновательный')
            $this->editCopy($changedAttributes);*/

        // тут должны работать проверки на ошибки
        $errorsCheck = new EventErrorsWork();
        $errorsCheck->CheckErrorsEventWithoutAmnesty($this->id);
    }

    private function copyEvent()
    {
        $fevent = new ForeignEvent();
        $fevent->name = $this->name;
        $fevent->start_date = $this->start_date;
        $fevent->finish_date = $this->finish_date;
        $fevent->event_level_id = $this->event_level_id;
        $fevent->key_words = $this->key_words;
        $fevent->copy = 1;
        $fevent->save(false);
    }

    private function editCopy($changedAttributes)
    {
        if ($changedAttributes["name"] !== null)
            $fevent = ForeignEvent::find()->where(['name' => $this->old_name])->one();
        else
            $fevent = ForeignEvent::find()->where(['name' => $this->name])->one();
        if ($fevent !== null)
        {
            $fevent->name = $this->name;
            $fevent->start_date = $this->start_date;
            $fevent->finish_date = $this->finish_date;
            $fevent->event_level_id = $this->event_level_id;
            $fevent->key_words = $this->key_words;
            $fevent->copy = 1;
            $fevent->save(false);
        }
        else
            $this->copyEvent();
    }

    public function getErrorsWork()
    {
        $errorsList = EventErrorsWork::find()->where(['event_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $error = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            $result .= 'Внимание, ошибка: ' . $error->number . ' ' . $error->name . '<br>';
        }
        return $result;
    }
}
