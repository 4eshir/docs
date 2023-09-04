<?php

use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\EventTypeWork;
use app\models\work\FocusWork;
use yii\db\Migration;

/**
 * Class m230808_113847_get_participants_data
 */
class m230808_113847_get_participants_data extends Migration
{
    public function init()
    {
        $this->db = Yii::$app->db_report_test;
        parent::init();
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //--Создаем тестовые мероприятия--
        $this->insert('get_participants_event', [
            'id' => 1,
            'name' => 'Test_event_1',
            'event_type_id' => EventTypeWork::COMPETITIVE,
            'event_form_id' => 1,
            'event_level_id' => EventLevelWork::INTERNAL,
            'finish_date' => '2022-01-01',
        ]);

        $this->insert('get_participants_event', [
            'id' => 2,
            'name' => 'Test_event_2',
            'event_type_id' => EventTypeWork::COMPETITIVE,
            'event_form_id' => 1,
            'event_level_id' => EventLevelWork::DISTRICT,
            'finish_date' => '2022-02-01',
        ]);

        $this->insert('get_participants_event', [
            'id' => 3,
            'name' => 'Test_event_3',
            'event_type_id' => EventTypeWork::COMPETITIVE,
            'event_form_id' => 1,
            'event_level_id' => EventLevelWork::DISTRICT,
            'finish_date' => '2022-03-01',
        ]);
        //--------------------------------

        //--Создание актов участия--
        $this->insert('get_participants_teacher_participant', ['id' => 1,
            'participant_id' => 1, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 1, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 2,
            'participant_id' => 2, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 1, 'focus' => FocusWork::SCIENCE, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 3,
            'participant_id' => 3, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 1, 'focus' => FocusWork::ART, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 4,
            'participant_id' => 4, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 1, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        //----------

        $this->insert('get_participants_teacher_participant', ['id' => 5,
            'participant_id' => 5, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 2, 'focus' => FocusWork::SPORT, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 6,
            'participant_id' => 2, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 2, 'focus' => FocusWork::SPORT, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        //----------

        $this->insert('get_participants_teacher_participant', ['id' => 7,
            'participant_id' => 1, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 3, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 8,
            'participant_id' => 3, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 3, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 9,
            'participant_id' => 6, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 3, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 10,
            'participant_id' => 7, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 3, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        $this->insert('get_participants_teacher_participant', ['id' => 11,
            'participant_id' => 8, 'teacher_id' => 0, 'teacher2_id' => 0, 'foreign_event_id' => 3, 'focus' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME,
        ]);

        //--------------------------


        //--Создание отделов учета--
        $this->insert('get_participants_teacher_participant_branch', ['id' => 1, 'branch_id' => BranchWork::TECHNO, 'teacher_participant_id' => 1]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 2, 'branch_id' => BranchWork::COD, 'teacher_participant_id' => 1]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 3, 'branch_id' => BranchWork::TECHNO, 'teacher_participant_id' => 2]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 4, 'branch_id' => BranchWork::QUANT, 'teacher_participant_id' => 3]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 5, 'branch_id' => BranchWork::COD, 'teacher_participant_id' => 4]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 6, 'branch_id' => BranchWork::CDNTT, 'teacher_participant_id' => 4]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 7, 'branch_id' => BranchWork::QUANT, 'teacher_participant_id' => 4]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 8, 'branch_id' => BranchWork::CDNTT, 'teacher_participant_id' => 5]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 9, 'branch_id' => BranchWork::CDNTT, 'teacher_participant_id' => 6]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 10, 'branch_id' => BranchWork::TECHNO, 'teacher_participant_id' => 7]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 11, 'branch_id' => BranchWork::QUANT, 'teacher_participant_id' => 7]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 12, 'branch_id' => BranchWork::MOB_QUANT, 'teacher_participant_id' => 8]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 13, 'branch_id' => BranchWork::MOB_QUANT, 'teacher_participant_id' => 9]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 14, 'branch_id' => BranchWork::TECHNO, 'teacher_participant_id' => 9]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 15, 'branch_id' => BranchWork::QUANT, 'teacher_participant_id' => 10]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 16, 'branch_id' => BranchWork::MOB_QUANT, 'teacher_participant_id' => 10]);
        $this->insert('get_participants_teacher_participant_branch', ['id' => 17, 'branch_id' => BranchWork::COD, 'teacher_participant_id' => 10]);

        $this->insert('get_participants_teacher_participant_branch', ['id' => 18, 'branch_id' => BranchWork::TECHNO, 'teacher_participant_id' => 11]);
        //--------------------------


        //--Создание команд--
        $this->insert('get_participants_team', ['id' => 1, 'name' => 'Team 1', 'teacher_participant_id' => 1]);
        $this->insert('get_participants_team', ['id' => 3, 'name' => 'Team 1', 'teacher_participant_id' => 4]);

        $this->insert('get_participants_team', ['id' => 4, 'name' => 'Team 2', 'teacher_participant_id' => 6]);
        $this->insert('get_participants_team', ['id' => 5, 'name' => 'Team 2', 'teacher_participant_id' => 8]);
        //-------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('get_participants_team', ['id' => 1]);
        $this->delete('get_participants_team', ['id' => 2]);
        $this->delete('get_participants_team', ['id' => 3]);
        $this->delete('get_participants_team', ['id' => 4]);
        $this->delete('get_participants_team', ['id' => 5]);

        $this->delete('get_participants_teacher_participant_branch', ['id' => 1]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 2]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 3]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 4]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 5]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 6]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 7]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 8]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 9]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 10]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 11]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 12]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 13]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 14]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 15]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 16]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 17]);
        $this->delete('get_participants_teacher_participant_branch', ['id' => 18]);

        $this->delete('get_participants_teacher_participant', ['id' => 1]);
        $this->delete('get_participants_teacher_participant', ['id' => 2]);
        $this->delete('get_participants_teacher_participant', ['id' => 3]);
        $this->delete('get_participants_teacher_participant', ['id' => 4]);
        $this->delete('get_participants_teacher_participant', ['id' => 5]);
        $this->delete('get_participants_teacher_participant', ['id' => 6]);
        $this->delete('get_participants_teacher_participant', ['id' => 7]);
        $this->delete('get_participants_teacher_participant', ['id' => 8]);
        $this->delete('get_participants_teacher_participant', ['id' => 9]);
        $this->delete('get_participants_teacher_participant', ['id' => 10]);
        $this->delete('get_participants_teacher_participant', ['id' => 11]);

        $this->delete('get_participants_event', ['id' => 1]);
        $this->delete('get_participants_event', ['id' => 2]);
        $this->delete('get_participants_event', ['id' => 3]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230808_113847_get_participants_data cannot be reverted.\n";

        return false;
    }
    */
}
