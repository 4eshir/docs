<?php

use yii\db\Migration;

/**
 * Class m230807_061807_create_tables_for_get_participants
 */
class m230807_061807_create_tables_for_get_participants extends Migration
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
        //--Создание таблиц--
        $this->createTable('get_participants_event', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000),
            'event_type_id' => $this->integer(),
            'event_form_id' => $this->integer(),
            'event_level_id' => $this->integer(),
            'finish_date' => $this->date(),
        ]);

        $this->createTable('get_participants_teacher_participant', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer(),
            'teacher_id' => $this->integer(),
            'teacher2_id' => $this->integer(),
            'foreign_event_id' => $this->integer(),
            'focus' => $this->integer(),
            'allow_remote_id' => $this->integer(),
        ]);

        $this->createTable('get_participants_teacher_participant_branch', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer(),
            'teacher_participant_id' => $this->integer(),
        ]);

        $this->createTable('get_participants_team', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000),
            'teacher_participant_id' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_teacher_participant',
            'get_participants_teacher_participant', 'foreign_event_id',
            'get_participants_event', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_teacher_participant_branch',
            'get_participants_teacher_participant_branch', 'teacher_participant_id',
            'get_participants_teacher_participant', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_team',
            'get_participants_team', 'teacher_participant_id',
            'get_participants_teacher_participant', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_teacher_participant_branch', 'get_participants_teacher_participant_branch');
        $this->dropForeignKey('key1_team', 'get_participants_team');

        $this->dropTable('get_participants_team');
        $this->dropTable('get_participants_teacher_participant_branch');
        $this->dropTable('get_participants_teacher_participant');
        $this->dropTable('get_participants_event');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230807_061807_create_tables_for_get_participants cannot be reverted.\n";

        return false;
    }
    */
}
