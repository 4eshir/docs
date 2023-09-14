<?php

use yii\db\Migration;

/**
 * Class m230914_123905_add_network_in_test_groups
 */
class m230914_123905_add_network_in_test_groups extends Migration
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
        $this->addColumn('get_group_participants_training_group', 'is_network', $this->integer());
        //---------------------------------------------------------------------

        //--Редактируем данные в таблице get_group_participants_training_group--
        $this->update('get_group_participants_training_group', ['is_network' => 1], ['id' => 1]);
        $this->update('get_group_participants_training_group', ['is_network' => 0], ['id' => 2]);
        $this->update('get_group_participants_training_group', ['is_network' => 0], ['id' => 3]);
        $this->update('get_group_participants_training_group', ['is_network' => 1], ['id' => 4]);
        $this->update('get_group_participants_training_group', ['is_network' => 0], ['id' => 5]);
        //----------------------------------------------------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('get_group_participants_training_group', 'is_network');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230914_123905_add_network_in_test_groups cannot be reverted.\n";

        return false;
    }
    */
}
