<?php

use yii\db\Migration;

/**
 * Class m240411_053943_base_table_2
 */
class m240411_053943_base_table_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auditorium}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'square' => $this->float(),
            'text' => $this->string(512)->null(),
            'capacity' => $this->integer()->null(),
            'files' => $this->string(1024)->null(),
            'is_education' => $this->boolean(),
            'branch_id' => $this->integer(),
            'include_square' => $this->boolean()->defaultValue(true),
            'window_count' => $this->smallInteger()->null(),
            'auditorium_type_id' => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk-auditorium-1',
            '{{%auditorium}}',
            'branch_id',
            '{{%branch}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-auditorium-2',
            '{{%auditorium}}',
            'auditorium_type_id',
            '{{%auditorium_type}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        $this->createTable('{{%bot_message_variant}}', [
            'id' => $this->primaryKey(),
            'bot_message_id' => $this->integer(),
            'text' => $this->string(512),
            'picture' => $this->string(1024),
            'next_bot_message_id' => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk-bot_message_variant-1',
            '{{%bot_message_variant}}',
            'bot_message_id',
            '{{%bot_message}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-bot_message_variant-2',
            '{{%bot_message_variant}}',
            'next_bot_message_id',
            '{{%bot_message}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        $this->createTable('{{%dropdown_characteristic_object}}', [
            'id' => $this->primaryKey(),
            'characteristic_object_id' => $this->integer(),
            'item' => $this->string(128),
        ]);

        $this->addForeignKey(
            'fk-dropdown_characteristic_object-1',
            '{{%dropdown_characteristic_object}}',
            'characteristic_object_id',
            '{{%characteristic_object}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        $this->createTable('{{%role_function}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'role_function_type_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-role_function-1',
            '{{%role_function}}',
            'role_function_type_id',
            '{{%role_function_type}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        $this->createTable('{{%people}}', [
            'id' => $this->primaryKey(),
            'secondname' => $this->string(128),
            'firstname' => $this->string(128),
            'patronymic' => $this->string(128)->null(),
            'company_id' => $this->integer()->null(),
            'position_id' => $this->integer()->null(),
            'short' => $this->string(16)->null(),
            'branch_id' => $this->integer()->null(),
            'birthdate' => $this->date()->null(),
            'sex' => $this->smallInteger()->null(),
            'genitive' => $this->string(128)->null(),
        ]);

        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'company_type_id' => $this->integer()->null(),
            'name' => $this->string(128),
            'short_name' => $this->string(128),
            'is_contractor' => $this->boolean(),
            'inn' => $this->string(15)->null(),
            'category_smsp_id' => $this->integer()->null(),
            'comment' => $this->string(256)->null(),
            'last_edit_id' => $this->integer()->null(),
            'phone_number' => $this->string(12)->null(),
            'email' => $this->string(128)->null(),
            'site' => $this->string(128)->null(),
            'ownership_type_id' => $this->integer()->null(),
            'okved' => $this->string(12)->null(),
            'head_fio' => $this->string(128)->null(),
        ]);

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'secondname' => $this->string(128),
            'firstname' => $this->string(128),
            'patronymic' => $this->string(128)->null(),
            'username' => $this->string(128)->unique(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string(256),
            'password_reset_token' => $this->string(256)->null(),
            'email' => $this->string(256)->null(),
            'aka' => $this->integer()->null(),
            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'creator_id' => $this->integer()->null(),
            'last_edit_id' => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk-people-1',
            '{{%people}}',
            'company_id',
            '{{%company}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-people-2',
            '{{%people}}',
            'position_id',
            '{{%position}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-people-3',
            '{{%people}}',
            'branch_id',
            '{{%branch}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-company-1',
            '{{%company}}',
            'company_type_id',
            '{{%company_type}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-company-2',
            '{{%company}}',
            'category_smsp_id',
            '{{%category_smsp}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-company-3',
            '{{%company}}',
            'last_edit_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-company-4',
            '{{%company}}',
            'ownership_type_id',
            '{{%ownership_type}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-user-1',
            '{{%user}}',
            'creator_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-user-2',
            '{{%user}}',
            'last_edit_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        $this->createTable('{{%access_level}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'role_function_id' => $this->integer(),
            'start_time' => $this->dateTime(),
            'end_time' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-access_level-1',
            '{{%access_level}}',
            'user_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-access_level-1',
            '{{%access_level}}',
            'role_function_id',
            '{{%role_function}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        $this->createTable('author_program', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer(),
            'training_program_id' => $this->integer(),
        ]);

        $this->createTable('training_program', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'thematic_direction_id' => $this->integer()->null(),
            'level' => $this->integer()->defaultValue(1),
            'ped_council_date' => $this->date()->null(),
            'ped_council_number' => $this->string(128)->null(),
            'author_id' => $this->integer()->null(),
            'capacity' => $this->integer()->defaultValue(0),
            'student_left_age' => $this->float()->defaultValue(5.0),
            'student_right_age' => $this->integer()->defaultValue(18),
            'focus_id' => $this->integer(),
            'allow_remote_id' => $this->integer(),
            'doc_file' => $this->string(1024)->null(),
            'edit_docs' => $this->string(1024)->null(),
            'key_words' => $this->string(1024)->null(),
            'hour_capacity' => $this->integer()->defaultValue(40),
            'actual' => $this->boolean()->defaultValue(false),
            'certificat_type_id' => $this->integer()->null(),
            'creator_id' => $this->integer()->null(),
            'description' => $this->text()->null(),
            'last_update_id' => $this->integer()->null(),
            'is_network' => $this->boolean()->null(),
            'contract' => $this->string(1024)->null(),
        ]);

        $this->addForeignKey(
            'fk-training_program-1',
            '{{%training_program}}',
            'thematic_direction_id',
            '{{%thematic_direction}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_program-2',
            '{{%training_program}}',
            'author_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_program-3',
            '{{%training_program}}',
            'focus_id',
            '{{%focus}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_program-4',
            '{{%training_program}}',
            'allow_remote_id',
            '{{%allow_remote}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_program-5',
            '{{%training_program}}',
            'certificat_type_id',
            '{{%certificat_type}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_program-6',
            '{{%training_program}}',
            'creator_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-training_program-7',
            '{{%training_program}}',
            'last_update_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-author_program-1',
            '{{%author_program}}',
            'author_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-author_program-2',
            '{{%author_program}}',
            'training_program_id',
            '{{%training_program}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240411_053943_base_table_2 cannot be reverted.\n";

        return false;
    }
}
