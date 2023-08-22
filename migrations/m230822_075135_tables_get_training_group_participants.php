<?php

use yii\db\Migration;

/**
 * Class m230822_075135_tables_get_training_group_participants
 */
class m230822_075135_tables_get_training_group_participants extends Migration
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
        $this->createTable('get_group_participants_training_program', [
            'id' => $this->primaryKey(),
            'focus_id' => $this->integer(),
            'allow_remote_id' => $this->integer(),
        ]);

        $this->createTable('get_group_participants_training_group', [
            'id' => $this->primaryKey(),
            'training_program_id' => $this->integer(),
        ]);

        $this->createTable('get_group_participants_training_group_participant', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer(),
            'training_group_id' => $this->integer(),
        ]);

        $this->createTable('get_group_participants_foreign_event_participant', [
            'id' => $this->primaryKey(),
            'birthdate' => $this->date(),
        ]);

        $this->createTable('get_group_participants_teacher_group', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer(),
            'training_group_id' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_training_group',
            'get_group_participants_training_group', 'training_program_id',
            'get_group_participants_training_program', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_training_group_participant',
            'get_group_participants_training_group_participant', 'participant_id',
            'get_group_participants_foreign_event_participant', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key2_training_group_participant',
            'get_group_participants_training_group_participant', 'training_group_id',
            'get_group_participants_training_group', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_teacher_group',
            'get_group_participants_teacher_group', 'training_group_id',
            'get_group_participants_training_group', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey ('key1_training_group', 'get_group_participants_training_group');
        $this->dropForeignKey('key1_training_group_participant', 'get_group_participants_training_group_participant');
        $this->dropForeignKey('key2_training_group_participant', 'get_group_participants_training_group_participant');
        $this->dropForeignKey('key1_teacher_group', 'get_group_participants_teacher_group');

        $this->dropTable('get_group_participants_training_program');
        $this->dropTable('get_group_participants_training_group');
        $this->dropTable('get_group_participants_training_group_participant');
        $this->dropTable('get_group_participants_foreign_event_participant');
        $this->dropTable('get_group_participants_teacher_group');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_075135_tables_get_training_group_participants cannot be reverted.\n";

        return false;
    }
    */
}
