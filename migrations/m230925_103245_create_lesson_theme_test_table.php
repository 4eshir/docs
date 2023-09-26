<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lesson_theme_test}}`.
 */
class m230925_103245_create_lesson_theme_test_table extends Migration
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
        //--Создание таблицы--
        $this->createTable('get_group_participants_lesson_theme', [
            'id' => $this->primaryKey(),
            'training_group_lesson_id' => $this->integer(),
            'teacher_id' => $this->integer(),
        ]);
        //--------------------

        //--Добавляем данные--
        $this->insert('get_group_participants_lesson_theme', ['id' => 1, 'training_group_lesson_id' => 38, 'teacher_id' => 1]);
        $this->insert('get_group_participants_lesson_theme', ['id' => 2, 'training_group_lesson_id' => 39, 'teacher_id' => 2]);
        $this->insert('get_group_participants_lesson_theme', ['id' => 3, 'training_group_lesson_id' => 40, 'teacher_id' => 2]);
        $this->insert('get_group_participants_lesson_theme', ['id' => 4, 'training_group_lesson_id' => 41, 'teacher_id' => 1]);
        //--------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_lesson_theme',
            'get_group_participants_lesson_theme', 'training_group_lesson_id',
            'get_group_participants_training_group_lesson', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('get_group_participants_lesson_theme');
    }
}
