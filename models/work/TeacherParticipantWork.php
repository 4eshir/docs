<?php

namespace app\models\work;

use app\models\common\Branch;
use app\models\common\ForeignEvent;
use app\models\common\ForeignEventParticipants;
use app\models\common\ParticipantFiles;
use app\models\common\People;
use app\models\common\TeacherParticipant;
use app\models\common\Team;
use app\models\components\FileWizard;
use app\models\null\ForeignEventNull;
use app\models\null\ForeignEventParticipantsNull;
use app\models\null\PeopleNull;
use app\models\null\TeacherParticipantBranchNull;
use Yii;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;


class TeacherParticipantWork extends TeacherParticipant
{
    public $team;
    public $file;
    public $fileString;

    public $branchs;

    function __construct($tId = null, $tParticipantId = null, $tTeacherId = null, $tTeacher2Id = null, $tForeignEventId = null, $tFocus = null, $tAllowRemoteId = null)
    {
        if ($tId === null)
            return;

        $this->id = $tId;
        $this->participant_id = $tParticipantId;
        $this->teacher_id = $tTeacherId;
        $this->teacher2_id = $tTeacher2Id;
        $this->foreign_event_id = $tForeignEventId;
        $this->focus = $tFocus;
        $this->allow_remote_id = $tAllowRemoteId;

        //--Дефолтные значения--
        $this->nomination = 'DEFAULT';
        //----------------------
    }

    public function rules()
    {
        return [
            [['participant_id', 'teacher_id', 'foreign_event_id'], 'required'],
            [['participant_id', 'teacher_id', 'teacher2_id', 'foreign_event_id', 'allow_remote_id', 'focus'], 'integer'],
            [['team', 'nomination'], 'string'],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['file'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true],
            ['branchs', 'safe'],
        ];
    }

    /**
 * @inheritdoc
 */
    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class' => 'lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior',
                'relations' => [
                    'teacherParticipantBranches',
                ],
            ],
        ];
    }

    public function getActString()
    {
        $part = ForeignEventParticipantsWork::find()->where(['id' => $this->participant_id])->one();
        $standard = TeamWork::find()->where(['teacher_participant_id' => $this->id])->one();

        if ($standard->team_name_id == null)
            $result = $part->fullName . ' ('. $this->focus0->name .' направленность, номинация: ' . $this->nomination . ') - Индивидуальное участие';
        else
        {
            $teamParts = \app\models\work\TeacherParticipantWork::find()->joinWith(['teams teams'])->where(['teacher_participant.foreign_event_id' => $this->foreign_event_id])
                ->andWhere(['teams.team_name_id' => $standard->team_name_id])->andWhere(['focus' => $this->focus])->andWhere(['nomination' => $this->nomination])->all();

            $result = 'Команда "' . $this->teamNameString . '" [участники: ';
            foreach ($teamParts as $part)
                $result .= $part->participantWork->shortName . ', ';
            $result = mb_substr($result, 0, -2) . ']' . ' ('. $this->focus0->name .' направленность, номинация: ' . $this->nomination . ')';
        }

        return $result;
    }

    public function getTeacherParticipantBranches()
    {
        $try = $this->hasMany(TeacherParticipantBranchWork::className(), ['teacher_participant_id' => 'id']);
        //return $try->all() ? $try : [new TeacherParticipantBranchNull];
        return $try;
    }


    public function getBranchs()
    {
        $funcs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $this->id])->all();
        $result = [];
        foreach ($funcs as $func)
            $result[] = $func->branch_id;

        return $result;
    }

    public function getBranchsString()
    {
        $funcs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $this->id])->all();
        $result = '';
        foreach ($funcs as $func)
            $result .= $func->branchWork->name . '<br>';
        $result = mb_substr($result, 0, -4);

        return $result;
    }

    public function getTeachersString()
    {
        $teacher1 = PeopleWork::find()->where(['id' => $this->teacher_id])->one();
        $result = mb_substr($teacher1->firstname, 0, 1) .'. '. mb_substr($teacher1->patronymic, 0, 1) .'. '. $teacher1->secondname;

        if ($this->teacher2_id != null)
        {
            $teacher2 = PeopleWork::find()->where(['id' => $this->teacher2_id])->one();
            $result .= '<br>' . mb_substr($teacher2->firstname, 0, 1) .'. '. mb_substr($teacher2->patronymic, 0, 1) .'. '. $teacher2->secondname;;
        }

        return $result;
    }

    public function getParticipantWork()
    {
        $try = $this->hasOne(ForeignEventParticipantsWork::className(), ['id' => 'participant_id']);
        return $try->all() ? $try : new ForeignEventParticipantsNull();
    }

    public function getForeignEventWork()
    {
        $try = $this->hasOne(ForeignEventWork::className(), ['id' => 'foreign_event_id']);
        return $try->all() ? $try : new ForeignEventNull();
    }

    public function getTeacherWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'teacher_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getTeacher2Work()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'teacher2_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $this->checkTeam();
        $funcs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $this->id])->all();
        foreach ($funcs as $func)
            $func->delete();

        if($this->branchs !== '' && $this->branchs !== null)
            foreach ($this->branchs as $branch)
            {
                $func = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $this->id])->andWhere(['branch_id' => $branch])->one();
                if ($func == null) $func = new TeacherParticipantBranchWork();
                $func->teacher_participant_id = $this->id;
                $func->branch_id = $branch;
                $func->save();
            }
    }

    public function getTeam()
    {
        $team = TeamWork::find()->where(['teacher_participant_id' => $this->id])->one();
        $this->team = $team === null ? '' : $team->team_name_id;
    }

    public function getTeamNameString()
    {
        $team = TeamWork::find()->where(['teacher_participant_id' => $this->id])->one();
        return $team->teamNameWork->name;
    }

    public function checkTeam()
    {
        $team = TeamWork::find()->where(['teacher_participant_id' => $this->id])->one();

        if ($this->team == null && $team == null)
            return;
        if ($team == null)
            $team = new TeamWork();
        if ($this->team == null)
        {
            $flag = $team->checkCollectionTeamName();
            $team_name_id = $team->team_name_id;

            $team->delete();
            if ($flag && $team_name_id != null);
            {
                $teamName = TeamNameWork::find()->where(['id' => $team_name_id])->one();
                $teamName->delete();
            }
            return;
        }

        $team->teacher_participant_id = $this->id;
        $team->team_name_id = $this->team;
        $team->save();
    }

    public function uploadParticipantFiles()
    {
        $path = '@app/upload/files/foreign-event/participants/';
        $date = $this->foreignEvent->start_date;
        $new_date = '';
        $filename = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $participant = ForeignEventParticipants::find()->where(['id' => $this->participant_id])->one();
        $filename = $participant->secondname.'_'.$new_date.'_'.$this->foreignEvent->name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^a-zA-Zа-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->fileString = $res.'.'.$this->file->extension;
        $this->file->saveAs( $path.$this->fileString);

        $partFile = ParticipantFilesWork::find()->where(['teacher_participant_id' => $this->id])->one();
        if ($partFile === null) $partFile = new ParticipantFilesWork();

        $partFile->teacher_participant_id = $this->id;
        $partFile->filename = $this->fileString;
        $partFile->save();
    }

    public function beforeSave($insert)
    {
        if ($this->allow_remote_id === null) $this->allow_remote_id = 1;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
