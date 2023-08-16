<?php

use yii\db\Migration;

/**
 * Class m230816_063353_add_team_architecture
 */
class m230816_063353_add_team_architecture extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //--Создание таблиц--
        $this->createTable('team_name', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000),
            'foreign_event_id' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_team_name',
            'team_name', 'foreign_event_id',
            'foreign_event', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------

        //--Изменение таблиц--
        $this->addColumn('team', 'team_name_id', $this->integer()->null());
        //--------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_team',
            'team', 'team_name_id',
            'team_name', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_team', 'team');
        $this->dropColumn('team', 'team_name_id');

        $this->dropForeignKey('key1_team_name', 'team_name');
        $this->dropTable('team_name');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_063353_add_team_architecture cannot be reverted.\n";

        return false;
    }
    */
}
