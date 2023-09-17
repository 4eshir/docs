<?php

use yii\db\Migration;

/**
 * Class m230816_094009_get_participants_team_architecture_update
 */
class m230816_094009_get_participants_team_architecture_update extends Migration
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
        $this->createTable('get_participants_team_name', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000),
            'foreign_event_id' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_get_participants_team_name',
            'get_participants_team_name', 'foreign_event_id',
            'get_participants_event', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------

        //--Изменение таблиц--
        $this->addColumn('get_participants_team', 'team_name_id', $this->integer()->null());
        //--------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_get_participants_team',
            'get_participants_team', 'team_name_id',
            'get_participants_team_name', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_get_participants_team', 'get_participants_team');
        $this->dropColumn('get_participants_team', 'team_name_id');

        $this->dropForeignKey('key1_get_participants_team_name', 'get_participants_team_name');
        $this->dropTable('get_participants_team_name');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_094009_get_participants_team_architecture_update cannot be reverted.\n";

        return false;
    }
    */
}
