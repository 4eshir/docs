<?php

use yii\db\Migration;

/**
 * Class m230814_125754_get_participant_achievements_participant_achievement
 */
class m230814_125754_get_participant_achievements_participant_achievement extends Migration
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
        $this->createTable('get_participant_achievements_participant_achievement', [
            'id' => $this->primaryKey(),
            'teacher_participant_id' => $this->integer(),
            'winner' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_participant_achievement',
            'get_participant_achievements_participant_achievement', 'teacher_participant_id',
            'get_participants_teacher_participant', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_participant_achievement', 'get_participant_achievements_participant_achievement');

        $this->dropTable('get_participant_achievements_participant_achievement');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230814_125754_get_participant_achievements_participant_achievement cannot be reverted.\n";

        return false;
    }
    */
}
