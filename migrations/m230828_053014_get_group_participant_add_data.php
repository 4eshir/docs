<?php

use yii\db\Migration;

/**
 * Class m230828_053014_get_group_participant_add_data
 */
class m230828_053014_get_group_participant_add_data extends Migration
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
        //--Добавляем столбцы в таблицу get_group_participants_training_group--
        $this->addColumn('get_group_participants_training_group', 'start_date', $this->date()->null());
        $this->addColumn('get_group_participants_training_group', 'finish_date', $this->date()->null());
        //---------------------------------------------------------------------

        //--Редактируем данные в таблице get_group_participants_training_group--
        $this->update('get_group_participants_training_group', ['start_date' => '2023-01-01'], ['id' => 1]);
        $this->update('get_group_participants_training_group', ['start_date' => '2023-02-12'], ['id' => 2]);
        $this->update('get_group_participants_training_group', ['start_date' => '2023-09-30'], ['id' => 3]);
        $this->update('get_group_participants_training_group', ['start_date' => '2023-03-02'], ['id' => 4]);
        $this->update('get_group_participants_training_group', ['start_date' => '2023-12-12'], ['id' => 5]);

        $this->update('get_group_participants_training_group', ['finish_date' => '2023-03-05'], ['id' => 1]);
        $this->update('get_group_participants_training_group', ['finish_date' => '2023-04-10'], ['id' => 2]);
        $this->update('get_group_participants_training_group', ['finish_date' => '2023-10-07'], ['id' => 3]);
        $this->update('get_group_participants_training_group', ['finish_date' => '2024-05-17'], ['id' => 4]);
        $this->update('get_group_participants_training_group', ['finish_date' => '2024-06-11'], ['id' => 5]);
        //-----------------------------------------------------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('get_group_participants_training_group', 'start_date');
        $this->dropColumn('get_group_participants_training_group', 'finish_date');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230828_053014_get_group_participant_add_data cannot be reverted.\n";

        return false;
    }
    */
}
