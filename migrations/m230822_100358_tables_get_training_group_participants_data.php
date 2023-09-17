<?php

use app\models\work\AllowRemoteWork;
use app\models\work\FocusWork;
use yii\db\Migration;

/**
 * Class m230822_100358_tables_get_training_group_participants_data
 */
class m230822_100358_tables_get_training_group_participants_data extends Migration
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
        //--Добавляем участников деятельности--
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 1, 'birthdate' => '2000-01-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 2, 'birthdate' => '2001-01-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 3, 'birthdate' => '2002-01-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 4, 'birthdate' => '2003-01-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 5, 'birthdate' => '2004-01-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 6, 'birthdate' => '2000-02-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 7, 'birthdate' => '2001-03-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 8, 'birthdate' => '2002-04-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 9, 'birthdate' => '2003-05-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 10, 'birthdate' => '2004-06-01']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 11, 'birthdate' => '2000-01-12']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 12, 'birthdate' => '2001-02-12']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 13, 'birthdate' => '2002-03-12']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 14, 'birthdate' => '2003-04-12']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 15, 'birthdate' => '2004-05-12']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 16, 'birthdate' => '2000-12-12']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 17, 'birthdate' => '2001-12-31']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 18, 'birthdate' => '2002-06-30']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 19, 'birthdate' => '2003-01-30']);
        $this->insert('get_group_participants_foreign_event_participant', ['id' => 20, 'birthdate' => '2004-09-22']);
        //-------------------------------------

        //--Добавляем образовательные программы--
        $this->insert('get_group_participants_training_program', ['id' => 1, 'focus_id' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME]);
        $this->insert('get_group_participants_training_program', ['id' => 2, 'focus_id' => FocusWork::TECHNICAL, 'allow_remote_id' => AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $this->insert('get_group_participants_training_program', ['id' => 3, 'focus_id' => FocusWork::ART, 'allow_remote_id' => AllowRemoteWork::FULLTIME]);
        $this->insert('get_group_participants_training_program', ['id' => 4, 'focus_id' => FocusWork::SPORT, 'allow_remote_id' => AllowRemoteWork::FULLTIME]);
        //---------------------------------------

        //--Добавляем учебные группы--
        $this->insert('get_group_participants_training_group', ['id' => 1, 'training_program_id' => 4]);
        $this->insert('get_group_participants_training_group', ['id' => 2, 'training_program_id' => 3]);
        $this->insert('get_group_participants_training_group', ['id' => 3, 'training_program_id' => 3]);
        $this->insert('get_group_participants_training_group', ['id' => 4, 'training_program_id' => 2]);
        $this->insert('get_group_participants_training_group', ['id' => 5, 'training_program_id' => 1]);
        //----------------------------

        //--Добавляем педагогов для групп--
        $this->insert('get_group_participants_teacher_group', ['id' => 1, 'teacher_id' => 1, 'training_group_id' => 1]);
        $this->insert('get_group_participants_teacher_group', ['id' => 2, 'teacher_id' => 2, 'training_group_id' => 3]);
        $this->insert('get_group_participants_teacher_group', ['id' => 3, 'teacher_id' => 3, 'training_group_id' => 5]);
        $this->insert('get_group_participants_teacher_group', ['id' => 4, 'teacher_id' => 4, 'training_group_id' => 4]);
        $this->insert('get_group_participants_teacher_group', ['id' => 5, 'teacher_id' => 5, 'training_group_id' => 2]);
        $this->insert('get_group_participants_teacher_group', ['id' => 6, 'teacher_id' => 1, 'training_group_id' => 1]);
        $this->insert('get_group_participants_teacher_group', ['id' => 7, 'teacher_id' => 2, 'training_group_id' => 3]);
        //---------------------------------

        //--Добавляем обучающихся в группы--
        $this->insert('get_group_participants_training_group_participant', ['id' => 1, 'participant_id' => 1, 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 2, 'participant_id' => 2, 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 3, 'participant_id' => 3, 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 4, 'participant_id' => 4, 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 5, 'participant_id' => 5, 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 6, 'participant_id' => 6, 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 7, 'participant_id' => 7, 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 8, 'participant_id' => 8, 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 9, 'participant_id' => 9, 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 10, 'participant_id' => 10, 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 11, 'participant_id' => 1, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 12, 'participant_id' => 2, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 13, 'participant_id' => 11, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 14, 'participant_id' => 12, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 15, 'participant_id' => 13, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 16, 'participant_id' => 14, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 17, 'participant_id' => 15, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 18, 'participant_id' => 16, 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 19, 'participant_id' => 7, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 20, 'participant_id' => 8, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 21, 'participant_id' => 9, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 22, 'participant_id' => 10, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 23, 'participant_id' => 11, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 24, 'participant_id' => 17, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 25, 'participant_id' => 18, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 26, 'participant_id' => 19, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 27, 'participant_id' => 20, 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 28, 'participant_id' => 14, 'training_group_id' => 5]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 29, 'participant_id' => 18, 'training_group_id' => 5]);
        $this->insert('get_group_participants_training_group_participant', ['id' => 30, 'participant_id' => 20, 'training_group_id' => 5]);
        //----------------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('get_group_participants_training_group_participant', ['id' => 1]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 2]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 3]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 4]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 5]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 6]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 7]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 8]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 9]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 10]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 11]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 12]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 13]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 14]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 15]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 16]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 17]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 18]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 19]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 20]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 21]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 22]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 23]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 24]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 25]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 26]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 27]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 28]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 29]);
        $this->delete('get_group_participants_training_group_participant', ['id' => 30]);

        $this->delete('get_group_participants_teacher_group', ['id' => 1]);
        $this->delete('get_group_participants_teacher_group', ['id' => 2]);
        $this->delete('get_group_participants_teacher_group', ['id' => 3]);
        $this->delete('get_group_participants_teacher_group', ['id' => 4]);
        $this->delete('get_group_participants_teacher_group', ['id' => 5]);
        $this->delete('get_group_participants_teacher_group', ['id' => 6]);
        $this->delete('get_group_participants_teacher_group', ['id' => 7]);

        $this->delete('get_group_participants_training_group', ['id' => 1]);
        $this->delete('get_group_participants_training_group', ['id' => 2]);
        $this->delete('get_group_participants_training_group', ['id' => 3]);
        $this->delete('get_group_participants_training_group', ['id' => 4]);
        $this->delete('get_group_participants_training_group', ['id' => 5]);

        $this->delete('get_group_participants_training_program', ['id' => 1]);
        $this->delete('get_group_participants_training_program', ['id' => 2]);
        $this->delete('get_group_participants_training_program', ['id' => 3]);
        $this->delete('get_group_participants_training_program', ['id' => 4]);

        $this->delete('get_group_participants_foreign_event_participant', ['id' => 1]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 2]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 3]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 4]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 5]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 6]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 7]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 8]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 9]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 10]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 11]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 12]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 13]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 14]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 15]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 16]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 17]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 18]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 19]);
        $this->delete('get_group_participants_foreign_event_participant', ['id' => 20]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_100358_tables_get_training_group_participants_data cannot be reverted.\n";

        return false;
    }
    */
}
