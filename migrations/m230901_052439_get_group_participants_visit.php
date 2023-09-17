<?php

use yii\db\Migration;

/**
 * Class m230901_052439_get_group_participants_visit
 */
class m230901_052439_get_group_participants_visit extends Migration
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
        $this->createTable('get_group_participants_training_group_lesson', [
            'id' => $this->primaryKey(),
            'lesson_date' => $this->date(),
            'training_group_id' => $this->integer(),
        ]);

        $this->createTable('get_group_participants_visit', [
            'id' => $this->primaryKey(),
            'foreign_event_participant_id' => $this->integer(),
            'training_group_lesson_id' => $this->integer(),
            'status' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_training_group_lesson',
            'get_group_participants_training_group_lesson', 'training_group_id',
            'get_group_participants_training_group', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_visit',
            'get_group_participants_visit', 'foreign_event_participant_id',
            'get_group_participants_foreign_event_participant', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key2_visit',
            'get_group_participants_visit', 'training_group_lesson_id',
            'get_group_participants_training_group_lesson', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------

        //--Добавляем данные--
        $this->insert('get_group_participants_training_group_lesson', ['id' => 1, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 2, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 3, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 4, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 5, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 6, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 7, 'lesson_date' => '2023-01-01', 'training_group_id' => 1]);

        $this->insert('get_group_participants_training_group_lesson', ['id' => 8, 'lesson_date' => '2023-02-12', 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 9, 'lesson_date' => '2023-02-12', 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 10, 'lesson_date' => '2023-02-12', 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 11, 'lesson_date' => '2023-02-12', 'training_group_id' => 2]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 12, 'lesson_date' => '2023-02-12', 'training_group_id' => 2]);

        $this->insert('get_group_participants_training_group_lesson', ['id' => 13, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 14, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 15, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 16, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 17, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 18, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 19, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 20, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 21, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 22, 'lesson_date' => '2023-09-30', 'training_group_id' => 3]);

        $this->insert('get_group_participants_training_group_lesson', ['id' => 23, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 24, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 25, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 26, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 27, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 28, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 29, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 30, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 31, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 32, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 33, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 34, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 35, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 36, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 37, 'lesson_date' => '2023-03-02', 'training_group_id' => 4]);

        $this->insert('get_group_participants_training_group_lesson', ['id' => 38, 'lesson_date' => '2023-12-12', 'training_group_id' => 5]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 39, 'lesson_date' => '2023-12-12', 'training_group_id' => 5]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 40, 'lesson_date' => '2023-12-12', 'training_group_id' => 5]);
        $this->insert('get_group_participants_training_group_lesson', ['id' => 41, 'lesson_date' => '2023-12-12', 'training_group_id' => 5]);


        //----Явки/неявки----
        $this->insert('get_group_participants_visit', ['id' => 1, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 1, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 2, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 2, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 3, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 3, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 4, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 4, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 5, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 5, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 6, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 6, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 7, 'foreign_event_participant_id' => 1, 'training_group_lesson_id' => 7, 'status' => 1]);

        $this->insert('get_group_participants_visit', ['id' => 8, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 1, 'status' => 3]);
        $this->insert('get_group_participants_visit', ['id' => 9, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 2, 'status' => 3]);
        $this->insert('get_group_participants_visit', ['id' => 10, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 3, 'status' => 2]);
        $this->insert('get_group_participants_visit', ['id' => 11, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 4, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 12, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 5, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 13, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 6, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 14, 'foreign_event_participant_id' => 2, 'training_group_lesson_id' => 7, 'status' => 0]);

        $this->insert('get_group_participants_visit', ['id' => 15, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 1, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 16, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 2, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 17, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 3, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 18, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 4, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 19, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 5, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 20, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 6, 'status' => 3]);
        $this->insert('get_group_participants_visit', ['id' => 21, 'foreign_event_participant_id' => 3, 'training_group_lesson_id' => 7, 'status' => 3]);

        $this->insert('get_group_participants_visit', ['id' => 22, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 1, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 23, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 2, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 24, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 3, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 25, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 4, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 26, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 5, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 27, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 6, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 28, 'foreign_event_participant_id' => 4, 'training_group_lesson_id' => 7, 'status' => 0]);

        $this->insert('get_group_participants_visit', ['id' => 29, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 1, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 30, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 2, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 31, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 3, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 32, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 4, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 33, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 5, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 34, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 6, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 35, 'foreign_event_participant_id' => 5, 'training_group_lesson_id' => 7, 'status' => 0]);

        $this->insert('get_group_participants_visit', ['id' => 36, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 1, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 37, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 2, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 38, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 3, 'status' => 2]);
        $this->insert('get_group_participants_visit', ['id' => 39, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 4, 'status' => 2]);
        $this->insert('get_group_participants_visit', ['id' => 40, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 5, 'status' => 2]);
        $this->insert('get_group_participants_visit', ['id' => 41, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 6, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 42, 'foreign_event_participant_id' => 6, 'training_group_lesson_id' => 7, 'status' => 0]);


        $this->insert('get_group_participants_visit', ['id' => 43, 'foreign_event_participant_id' => 14, 'training_group_lesson_id' => 38, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 44, 'foreign_event_participant_id' => 14, 'training_group_lesson_id' => 39, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 45, 'foreign_event_participant_id' => 14, 'training_group_lesson_id' => 40, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 46, 'foreign_event_participant_id' => 14, 'training_group_lesson_id' => 41, 'status' => 0]);

        $this->insert('get_group_participants_visit', ['id' => 47, 'foreign_event_participant_id' => 18, 'training_group_lesson_id' => 38, 'status' => 1]);
        $this->insert('get_group_participants_visit', ['id' => 48, 'foreign_event_participant_id' => 18, 'training_group_lesson_id' => 39, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 49, 'foreign_event_participant_id' => 18, 'training_group_lesson_id' => 40, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 50, 'foreign_event_participant_id' => 18, 'training_group_lesson_id' => 41, 'status' => 1]);

        $this->insert('get_group_participants_visit', ['id' => 51, 'foreign_event_participant_id' => 20, 'training_group_lesson_id' => 38, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 52, 'foreign_event_participant_id' => 20, 'training_group_lesson_id' => 39, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 53, 'foreign_event_participant_id' => 20, 'training_group_lesson_id' => 40, 'status' => 0]);
        $this->insert('get_group_participants_visit', ['id' => 54, 'foreign_event_participant_id' => 20, 'training_group_lesson_id' => 41, 'status' => 0]);
        //-------------------


        //--------------------

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_training_group_lesson', 'get_group_participants_training_group_lesson');
        $this->dropForeignKey('key1_visit', 'get_group_participants_visit');
        $this->dropForeignKey('key2_visit', 'get_group_participants_visit');

        $this->dropTable('get_group_participants_visit');
        $this->dropTable('get_group_participants_training_group_lesson');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230901_052439_get_group_participants_visit cannot be reverted.\n";

        return false;
    }
    */
}
