<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property string $name
 * @property string $start_date
 * @property string $finish_date
 * @property int $event_type_id
 * @property int $event_form_id
 * @property string $address
 * @property int $event_level_id
 * @property int $participants_count
 * @property int $is_federal
 * @property int $responsible_id
 * @property string $key_words
 * @property string $comment
 * @property int $order_id
 * @property int $regulation_id
 * @property string $protocol
 * @property string $photos
 * @property string $reporting_doc
 * @property string $other_files
 *
 * @property EventForm $eventForm
 * @property EventLevel $eventLevel
 * @property EventType $eventType
 * @property EventsLink[] $eventsLink
 * @property DocumentOrder $order
 * @property Regulation $regulation
 * @property People $responsible
 */
class Event extends \yii\db\ActiveRecord
{
    public $protocolFile;
    public $photoFiles;
    public $reportingFile;
    public $otherFiles;
    public $eventsLink;

    public $isTechnopark;
    public $isQuantorium;
    public $isCDNTT;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'finish_date', 'event_type_id', 'event_form_id', 'address', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'order_id', 'regulation_id', 'protocol'], 'required'],
            [['start_date', 'finish_date'], 'safe'],
            [['event_type_id', 'event_form_id', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'order_id', 'regulation_id', 'isTechnopark', 'isQuantorium', 'isCDNTT'], 'integer'],
            [['address', 'key_words', 'comment', 'protocol', 'photos', 'reporting_doc', 'other_files', 'name'], 'string', 'max' => 1000],
            [['event_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventForm::className(), 'targetAttribute' => ['event_form_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevel::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['event_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventType::className(), 'targetAttribute' => ['event_type_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['regulation_id' => 'id']],
            [['responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['responsible_id' => 'id']],
            [['protocolFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['photoFiles'], 'file', 'extensions' => 'jpg, png, jpeg, gif', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['reportingFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['otherFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Дата начала',
            'finish_date' => 'Дата окончания',
            'event_type_id' => 'Тип мероприятия',
            'event_form_id' => 'Форма мероприятия',
            'address' => 'Адрес проведения',
            'event_level_id' => 'Уровень мероприятия',
            'participants_count' => 'Кол-во участников',
            'is_federal' => 'Входит в ФП',
            'responsible_id' => 'Ответственный работник',
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
            'isCDNTT' => 'ЦДНТТ',
        ];
    }

    /**
     * Gets query for [[EventForm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventForm()
    {
        return $this->hasOne(EventForm::className(), ['id' => 'event_form_id']);
    }

    /**
     * Gets query for [[EventLevel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventLevel()
    {
        return $this->hasOne(EventLevel::className(), ['id' => 'event_level_id']);
    }

    /**
     * Gets query for [[EventType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventType()
    {
        return $this->hasOne(EventType::className(), ['id' => 'event_type_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'order_id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::className(), ['id' => 'regulation_id']);
    }

    /**
     * Gets query for [[Responsible]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible()
    {
        return $this->hasOne(People::className(), ['id' => 'responsible_id']);
    }

    /**
     * Gets query for [[EventsLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventsLink()
    {
        return $this->hasMany(EventsLink::className(), ['event_id' => 'id']);
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
                $filename = 'Пр'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Пр'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            $filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
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
                $filename = 'Яв'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Яв'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            $filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
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
            $filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
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
                $filename = 'Файл'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'_'.$this->name;
            else
                $filename = 'Файл'.$counter.'_'.$new_date.'_'.$this->order->order_number.'-'.$this->order->order_copy_id.'-'.$this->order->order_postfix.'_'.$this->name;
            $filename = $filename.'_'.$this->getEventNumber();
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
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
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($this->eventsLink !== null)
        {
            foreach ($this->eventsLink as $eventLink)
            {
                if ($eventLink->eventExternalName !== '')
                {
                    $evnLnk = new EventsLink();
                    $evnLnk->event_id = $this->id;
                    $evnLnk->event_external_id = EventExternal::find()->where(['name' => $eventLink->eventExternalName])->one()->id;
                    $evnLnk->save(false);
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
    }
}