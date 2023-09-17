<?php

use yii\db\Migration;

/**
 * Class m230912_081722_get_group_participant_update_test_architecture
 */
class m230912_081722_get_group_participant_update_test_architecture extends Migration
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

        //--Редактируем данные в таблице get_group_participant_update_test_architecture--
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 1]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-15'], ['id' => 2]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-01'], ['id' => 3]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-15'], ['id' => 4]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-01'], ['id' => 5]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-03'], ['id' => 6]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-05'], ['id' => 7]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-12'], ['id' => 8]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-01'], ['id' => 9]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-25'], ['id' => 10]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-04-02'], ['id' => 11]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-04-10'], ['id' => 12]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 13]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 14]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-01'], ['id' => 15]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-01'], ['id' => 16]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-02'], ['id' => 17]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-03'], ['id' => 18]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-04'], ['id' => 19]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-05'], ['id' => 20]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-06'], ['id' => 21]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-07'], ['id' => 22]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 23]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-04-02'], ['id' => 24]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-05-02'], ['id' => 25]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-06-02'], ['id' => 26]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-07-02'], ['id' => 27]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-08-02'], ['id' => 28]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-02'], ['id' => 29]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-10-02'], ['id' => 30]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-11-02'], ['id' => 31]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-12-02'], ['id' => 32]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-02-02'], ['id' => 33]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-03-02'], ['id' => 34]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-04-02'], ['id' => 35]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-05-02'], ['id' => 36]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-06-02'], ['id' => 37]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-12-12'], ['id' => 38]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-02-08'], ['id' => 39]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-04-20'], ['id' => 40]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2024-06-11'], ['id' => 41]);
        //-------------------------------------------------------------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 1]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 2]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 3]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 4]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 5]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 6]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-01-01'], ['id' => 7]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-12'], ['id' => 8]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-12'], ['id' => 9]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-12'], ['id' => 10]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-12'], ['id' => 11]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-02-12'], ['id' => 12]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 13]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 14]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 15]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 16]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 17]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 18]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 19]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 20]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 21]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-09-30'], ['id' => 22]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 23]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 24]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 25]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 26]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 27]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 28]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 29]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 30]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 31]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 32]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 33]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 34]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 35]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 36]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-03-02'], ['id' => 37]);

        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-12-12'], ['id' => 38]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-12-12'], ['id' => 39]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-12-12'], ['id' => 40]);
        $this->update('get_group_participants_training_group_lesson', ['lesson_date' => '2023-12-12'], ['id' => 41]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230912_081722_get_group_participant_update_test_architecture cannot be reverted.\n";

        return false;
    }
    */
}
